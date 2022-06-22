import Dexie from "dexie";

export const db = new Dexie('tooman-crm')

db.version(3).stores({
    arrivals: '++id, products, child_store, comment, arrivedAt, paymentCost, moneyRate',
});

db.version(3).stores({
    transfers: '++id, cart, storeFilter, child_store'
})


