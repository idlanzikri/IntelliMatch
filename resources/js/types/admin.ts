export interface AdminCareer {
    id: number;
    title: string;
    sector: string;
    description: string;
    skills: string[];
    is_active: boolean;
}
export interface Paginated<T> {
    data: T[];
    current_page: number;
    last_page: number;
    total: number;
    prev_page_url: string | null;
    next_page_url: string | null;
}
export interface AdminStudent {
    id: number;
    name: string;
    email: string;
    verified: boolean;
    programme: string | null;
    institution: string | null;
    skillsCount: number;
}
