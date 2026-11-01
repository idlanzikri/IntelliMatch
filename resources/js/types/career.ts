export interface StudentProfile {
    institution: string | null;
    programme: string | null;
    location: string | null;
    interests: string | null;
    skills: string[] | null;
}
export interface CareerMatch {
    id: string;
    title: string;
    sector: string;
    description: string;
    skills: string[];
    matched: string[];
    missing: string[];
    score: number;
}
