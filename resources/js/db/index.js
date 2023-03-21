import Dexie from "dexie";

export const db = new Dexie('iron-addicts')

db.version(3).stores({
    arrivals: '++id, products, child_store, comment, arrivedAt, paymentCost, moneyRate',
});

db.version(3).stores({
    transfers: '++id, cart, storeFilter, child_store'
})

db.version(4).stores({
    iherb: '++id, cart'
})


