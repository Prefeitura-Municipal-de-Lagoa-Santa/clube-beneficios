export interface User {
    id: number;
    name: string;
    username: string;
    email: string;
    matricula: string | null;
    guid: string | null;
    domain: string | null;
    roles: string[];
    permissions: string[];
    member_card: MemberCard | null;
    created_at: string;
    updated_at: string;
}

export interface Role {
    id: number;
    name: string;
    guard_name: string;
    permissions: Permission[];
    created_at: string;
    updated_at: string;
}

export interface Permission {
    id: number;
    name: string;
    guard_name: string;
    created_at: string;
    updated_at: string;
}

export interface Category {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    icon: string | null;
    active: boolean;
    sort_order: number;
    partners_count?: number;
    partners?: Partner[];
    created_at: string;
    updated_at: string;
}

export interface Partner {
    id: number;
    category_id: number;
    category?: Category;
    name: string;
    cnpj: string | null;
    logo_url: string | null;
    address: string | null;
    phone: string | null;
    email: string | null;
    website: string | null;
    description: string | null;
    active: boolean;
    benefits?: Benefit[];
    benefits_count?: number;
    created_at: string;
    updated_at: string;
}

export interface Benefit {
    id: number;
    partner_id: number;
    partner?: Partner;
    title: string;
    description: string;
    active: boolean;
    sort_order: number;
    created_at: string;
    updated_at: string;
}

export interface MemberCard {
    id: number;
    user_id: number;
    token: string;
    issued_at: string;
    created_at: string;
    updated_at: string;
}

export interface Auth {
    user: User;
}

export interface AppPageProps {
    auth: Auth;
    flash: {
        success?: string;
        error?: string;
    };
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: string;
    permission?: string;
}

export interface PaginatedData<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
}
