export default class {

    getMarginPercentage (product_price, purchase_price) {
        const profit = product_price - purchase_price;
        return Math.round(profit * 100 / product_price);
    }

    getMarginLevel (product_price, purchase_price) {
        const percentage = this.getMarginPercentage(product_price, purchase_price);
        if (percentage < 30) {
            return 'red--text';
        }
        if (percentage < 45) {
            return 'yellow--text';
        }
        return 'green-text';
    }

    getSurchargePercentage (product_price, purchase_price) {
        const profit = product_price - purchase_price;
        if (purchase_price === 0) {
            return 0;
        }
        return Math.round(profit * 100 / purchase_price) ?? 0;
    }

    getSurchargeLevel (product_price, purchase_price) {
        const percentage = this.getSurchargePercentage(product_price, purchase_price);
        if (percentage < 30) {
            return 'red--text';
        }
        if (percentage < 45) {
            return 'yellow--text';
        }
        return 'green--text';
    }
}
