export * from './auth';

import type { Auth } from './auth';

export interface CartItem {
    id: string;
    name: string;
    quantity: number;
    unitPrice: number;
    taxRate: number;
    total: number;
    subTotal: number;
}

export interface Cart {
    items: CartItem[];
    itemCount: number;
    total: number;
    subTotal: number;
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string;
    auth: Auth;
    cart: Cart;
    [key: string]: unknown;
};
