export interface Company {
    latitude: number | null;
    longitude: number | null;
    location_is_approximate: boolean;
    id: number;
    name: string;
    industry: string;
    location: string;
    description: string;
    website: string | null;
    is_approved: boolean;
}
export interface Vacancy {
    match?: { score: number; skills_score: number; qualification_score: number | null; matched: string[]; missing: string[] } | null;
    distance_km?: number | null;
    location_is_approximate: boolean;
    latitude: number | null;
    longitude: number | null;
    qualification_level?: string | null;
    applications_count?: number;
    id: number;
    title: string;
    description: string;
    type: string;
    arrangement: string;
    location: string;
    salary_min: number | null;
    salary_max: number | null;
    skills: string[];
    qualifications: string | null;
    career_id: number | null;
    closing_date: string;
    application_url: string;
    status: string;
    company?: Company;
}
