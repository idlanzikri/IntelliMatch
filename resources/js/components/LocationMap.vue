<script setup lang="ts">
import { onMounted, onBeforeUnmount, ref, watch } from "vue";
import type { Map as LibreMap, GeoJSONSource, Marker } from "maplibre-gl";
import type { FeatureCollection } from "geojson";
import workerUrl from "maplibre-gl/dist/maplibre-gl-worker.mjs?url";
import "maplibre-gl/dist/maplibre-gl.css";
const props = withDefaults(
    defineProps<{
        points?: {
            id: number;
            name: string;
            latitude: number;
            longitude: number;
        }[];
        editable?: boolean;
        latitude?: number | null;
        longitude?: number | null;
        selectedId?: number | null;
        fitToPoints?: boolean;
    }>(),
    { points: () => [] },
);
const emit = defineEmits<{
    pick: [latitude: number, longitude: number];
    select: [id: number];
}>();
const container = ref<HTMLDivElement>();
const error = ref("");
let map: LibreMap | undefined;
let marker: Marker | undefined;
let disposed = false;
let observer: ResizeObserver | undefined;
let fittedBounds = '';
function fitPoints() {
    if (!map || !props.fitToPoints || !props.points.length) return;
    const xs = props.points.map(p => p.longitude);
    const ys = props.points.map(p => p.latitude);
    const bounds: [number, number, number, number] = [Math.min(...xs), Math.min(...ys), Math.max(...xs), Math.max(...ys)];
    const key = bounds.join(',');
    if (key === fittedBounds) return;
    fittedBounds = key;
    map.fitBounds(bounds, { padding: 50, maxZoom: 10, duration: 500 });
}
function data(): FeatureCollection {
    const selected = props.points.find(p => p.id === props.selectedId);
    return {
        type: "FeatureCollection",
        features: props.points.map((p) => ({
            type: "Feature",
            properties: { id: p.id, name: p.name, selected: !!selected && p.latitude === selected.latitude && p.longitude === selected.longitude },
            geometry: { type: "Point", coordinates: [p.longitude, p.latitude] },
        })),
    };
}
function update() {
    (map?.getSource("places") as GeoJSONSource | undefined)?.setData(data());
    fitPoints();
}
onMounted(async () => {
    try {
        const lib = await import("maplibre-gl");
        lib.setWorkerUrl(workerUrl);
        if (disposed || !container.value) return;
        map = new lib.Map({
            container: container.value,
            center:
                props.longitude != null && props.latitude != null
                    ? [props.longitude, props.latitude]
                    : [108, 4],
            zoom: props.longitude != null ? 12 : 5,
            style: import.meta.env.VITE_MAP_STYLE_URL || {
                version: 8,
                sources: {
                    basemap: {
                        type: "raster",
                        tiles: [
                            "https://tile.openstreetmap.org/{z}/{x}/{y}.png",
                        ],
                        tileSize: 256,
                        maxzoom: 19,
                        attribution:
                            '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                    },
                },
                layers: [{ id: "basemap", type: "raster", source: "basemap" }],
            },
        });
        map.addControl(new lib.NavigationControl(), "top-right");
        map.on("error", () => {
            error.value =
                "Some map tiles could not load. Check your connection; the results list is still available.";
        });
        if (props.editable) {
            marker = new lib.Marker({ color: "#0f766e", scale: 0.65 });
            if (props.latitude != null && props.longitude != null)
                marker.setLngLat([props.longitude, props.latitude]).addTo(map);
            map.on("click", (e) => {
                marker?.setLngLat(e.lngLat).addTo(map!);
                emit(
                    "pick",
                    Number(e.lngLat.lat.toFixed(7)),
                    Number(e.lngLat.lng.toFixed(7)),
                );
            });
        }
        map.on("load", () => {
            if (!map) return;
            map.addSource("places", {
                type: "geojson",
                data: data(),
                cluster: true,
                clusterRadius: 40,
                clusterMaxZoom: 5,
            });
            map.addLayer({
                id: "clusters",
                type: "circle",
                source: "places",
                filter: ["has", "point_count"],
                paint: {
                    "circle-color": "#0f766e",
                    "circle-radius": 10,
                    "circle-stroke-color": "#fff",
                    "circle-stroke-width": 2,
                },
            });
            map.addLayer({
                id: "points",
                type: "circle",
                source: "places",
                filter: ["!", ["has", "point_count"]],
                paint: {
                    "circle-color": ['case', ['boolean', ['get', 'selected'], false], '#ea580c', '#0f766e'],
                    "circle-radius": 4,
                    "circle-stroke-color": "#fff",
                    "circle-stroke-width": 1,
                },
            });
            map.on("click", "points", (e) => {
                const feature = e.features?.[0];
                if (!feature) return;
                emit("select", Number(feature.properties.id));
            });
            map.on("click", "clusters", async (e) => {
                const f = e.features?.[0];
                if (!f || !map || f.geometry.type !== "Point") return;
                const zoom = await (
                    map.getSource("places") as GeoJSONSource
                ).getClusterExpansionZoom(Number(f.properties.cluster_id));
                if (!disposed)
                    map.easeTo({
                        center: f.geometry.coordinates as [number, number],
                        zoom,
                    });
            });
            for (const layer of ["points", "clusters"]) {
                map.on("mouseenter", layer, () => {
                    if (map) map.getCanvas().style.cursor = "pointer";
                });
                map.on("mouseleave", layer, () => {
                    if (map) map.getCanvas().style.cursor = "";
                });
            }
            fitPoints();
        });
        observer = new ResizeObserver(() => map?.resize());
        observer.observe(container.value);
    } catch {
        error.value =
            "Map unavailable on this device. You can still use the results list or enter coordinates below.";
    }
});
watch(() => props.points, update);
watch(() => props.selectedId, update);
watch(() => props.fitToPoints, () => { fittedBounds = ''; fitPoints(); });
watch(
    () => [props.latitude, props.longitude],
    () => {
        if (props.latitude != null && props.longitude != null && map)
            marker?.setLngLat([props.longitude, props.latitude]).addTo(map);
        else marker?.remove();
    },
);
onBeforeUnmount(() => {
    disposed = true;
    observer?.disconnect();
    marker?.remove();
    map?.remove();
});
</script>
<template>
    <div>
        <div
            ref="container"
            class="h-[440px] w-full overflow-hidden rounded-xl border"
            :aria-label="
                editable ? 'Select workplace location on map' : 'Locations map'
            "
        />
        <p v-if="error" role="status" class="mt-2 text-sm text-amber-800">
            {{ error }}
        </p>
        <p v-if="editable" class="mt-2 text-sm text-slate-500">
            Click the map to place or move the pin. Zoom in to confirm the exact
            entrance.
        </p>
    </div>
</template>
