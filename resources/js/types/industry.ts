export interface Company {
    id: number;
    name: string;
    industry: string;
    location: string;
    description: string;
    website: string | null;
    is_approved: boolean;
}
export interface Vacancy {
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
