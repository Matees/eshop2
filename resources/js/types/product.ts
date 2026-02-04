export interface Product {
    id: number;
    name: string;
    image: string;
    description: string;
    price: string;
    price_with_vat: string;
    tax_rate: string;
}

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface Paginated<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: PaginationLink[];
}

export interface CursorPaginated<T> {
    data: T[];
    per_page: number;
    next_cursor: string | null;
    prev_cursor: string | null;
    next_page_url: string | null;
    prev_page_url: string | null;
}

export interface PriceRange {
    min_price: number;
    max_price: number;
}

export interface PriceFilters {
    min_price?: string;
    max_price?: string;
}
