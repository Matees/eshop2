export * from './auth';

import type { Auth } from './auth';

export interface CartItem {
    id: string;
    name: string;
    quantity: number;
    unitPrice: number;
    taxRate: number;
}

export interface Cart {
    items: CartItem[];
    itemCount: number;
    total: number;
    subtotal: number;
}

export interface Flash {
    success: string | null;
    error: string | null;
    warning: string | null;
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: Auth;
    cart: Cart;
    flash: Flash;
    name: string;
    [key: string]: unknown;
};
