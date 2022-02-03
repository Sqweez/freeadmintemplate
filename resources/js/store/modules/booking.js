import ACTIONS from '@/store/actions'
import MUTATIONS from '@/store/mutations'
import {createBooking, createBookingSale, deleteBooking, getBookings} from "@/api/booking";

const bookingModule = {
    state: {
        bookings: [],
    },
    getters: {},
    mutations: {
        async [MUTATIONS.SET_BOOKINGS] (state, bookings) {
            state.bookings = bookings;
        },
        [MUTATIONS.DELETE_BOOKING] (state, bookingId) {
            state.bookings = state.bookings.filter(b => b.id !== bookingId);
        }
    },
    actions: {
        async [ACTIONS.GET_BOOKINGS]({commit}) {
            try {
                this.$loading.enable();
                const { data } = await getBookings();
                commit(MUTATIONS.SET_BOOKINGS, data.data);
            } catch (e) {
                throw e;
            } finally {
                this.$loading.disable();
            }
        },
        async [ACTIONS.DELETE_BOOKING]({commit}, bookingId) {
            try {
                this.$loading.enable();
                await deleteBooking(bookingId);
                commit(MUTATIONS.DELETE_BOOKING, bookingId);
            } catch (e) {
                throw e;
            } finally {
                this.$loading.disable();
            }
        },
        async [ACTIONS.CREATE_BOOKING]({commit}, booking) {
            try {
                this.$loading.enable();
                await createBooking(booking);
            } catch (e) {
                throw e;
            } finally {
                this.$loading.disable();
            }
        },
        async MAKE_BOOKING_SALE_v2({commit}, sale) {
            try {
                this.$loading.enable();
                await createBookingSale(sale);
            } catch (e) {
                throw e;
            } finally {
                this.$loading.disable();
            }
        }
    }
}

export default bookingModule;
