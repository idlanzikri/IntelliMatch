export interface StudentProfile { qualification_level?: string | null;
    institution: string | null;
    programme: string | null;
    location: string | null;
    interests: string | null;
    skills: string[] | null;
}
export interface CareerMatch {
    id: number;
    title: string;
    sector: string;
    description: string;
    skills: string[];
    matched: string[];
    missing: string[];
    score: number;
}
