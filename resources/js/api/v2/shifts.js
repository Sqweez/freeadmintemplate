import axiosClient from "@/utils/axiosClient";

export async function openShift(payload) {
    return await axiosClient.post(`v2/shift`, payload);
}

export async function getShiftTaxes() {
    return await axiosClient.get(`v2/shift/tax`);
}

export async function saveShiftTaxes(taxes) {
    return await axiosClient.post(`v2/shift/tax`, {
        taxes
    });
}

export async function getShiftPenalties() {
    return await axiosClient.get(`v2/shift/penalty`);
}

export async function createShiftPenalty(penalty) {
    return await axiosClient.post(`v2/shift/penalty`, penalty);
}

export async function deleteShiftPenalty(id) {
    return await axiosClient.delete(`v2/shift/penalty/${id}`);
}

export async function getPayrolls(period) {
    return await axiosClient.get(`v2/shift/payroll?date=${period}`);
}

export async function getShifts(date) {
    return await axiosClient.get(`v2/shift?date=${date}`);
}

export async function createShift(payload) {
    return await axiosClient.post(`v2/shift/create`, payload);
}

export async function editShift(payload) {
    return await axiosClient.patch(`v2/shift/${payload.shift.id}`, payload);
}
