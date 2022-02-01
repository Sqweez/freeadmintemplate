import moment from 'moment';
import MONTHS from "@/common/enums/months.ru";

export default class DatePlugin {
    parseMonthsDiff(num = 6) {
        let dateEnd = moment();
        let dateStart = moment().subtract(num - 1, 'months');
        let interim = dateStart.clone();
        let timeValues = [];

        while (dateEnd > interim || interim.format('M') === dateEnd.format('M')) {
            timeValues.push(interim.format('YYYY-MM'));
            interim.add(1,'month');
        }

        return timeValues.map((m) => {
            const dates = m.split('-');
            const year = dates[0];
            const month = MONTHS[parseInt(dates[1]) - 1];
            return {
                name: `${month}, ${year} г.`,
                value: m + '-01',
            };
        }).reverse();
    }
}
