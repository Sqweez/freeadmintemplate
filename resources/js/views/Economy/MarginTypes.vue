<template>
    <div>
        <v-card>
            <v-card-title>
                Типы маржинальности
            </v-card-title>
            <v-card-text>
                <v-simple-table v-slot:default>
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Название</th>
                        <th>Правила кэшбека тренерам</th>
                        <th>Правила зарплат</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(type, key) of marginTypes" :key="key">
                        <td>{{ key + 1 }}</td>
                        <td>{{ type.title }}</td>
                        <td>
                            <div class="d-flex align-center justify-space-between">
                                <ul v-if="type.partner_cashback_rules && type.partner_cashback_rules.length > 0">
                                    <li v-for="(rule, cashbackKey) of type.partner_cashback_rules" :key="`cashback-rule-${cashbackKey}`" class="d-flex align-center">
                                        <v-text-field
                                            label="Порог"
                                            v-model="rule.threshold"
                                        />
                                        <v-text-field
                                            label="%, кэшбека"
                                            v-model="rule.value"
                                        />
                                        <v-btn icon color="error" @click="deleteCashbackRule(key, cashbackKey)">
                                            <v-icon>mdi-close</v-icon>
                                        </v-btn>
                                    </li>
                                </ul>
                                <p v-else>Правила не установлены</p>
                                <v-btn icon color="success" @click="addCashbackRule(key)">
                                    <v-icon>
                                        mdi-plus
                                    </v-icon>
                                </v-btn>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-center justify-space-between">
                                <ul v-if="type.salary_rules && type.salary_rules.length > 0">
                                    <li v-for="(rule, salaryKey) of type.salary_rules" :key="`salary-rule-${salaryKey}`" class="d-flex align-center">
                                        <v-text-field
                                            label="Порог"
                                            v-model="rule.threshold"
                                        />
                                        <v-text-field
                                            label="%, зарплаты"
                                            v-model="rule.value"
                                        />
                                        <v-btn icon color="error" @click="deleteSalaryRule(key, salaryKey)">
                                            <v-icon>mdi-close</v-icon>
                                        </v-btn>
                                    </li>
                                </ul>
                                <p v-else>Правила не установлены</p>
                                <v-btn icon color="success" @click="addSalaryRule(key)">
                                    <v-icon>
                                        mdi-plus
                                    </v-icon>
                                </v-btn>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </v-simple-table>
                <div class="d-flex justify-end">
                    <v-btn color="success" style="margin-left: auto" @click="onSave">
                        Сохранить <v-icon>mdi-check</v-icon>
                    </v-btn>
                </div>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>

import ACTIONS from "@/store/actions";

export default {
    data: () => ({
        marginTypes: [],
    }),
    computed: {
        types () {
            return this.$store.getters.MARGIN_TYPES;
        },
    },
    watch: {
        types: {
            immediate: true,
            handler(val) {
                this.marginTypes = [...val];
            },
        }
    },
    methods: {
        addCashbackRule(key) {
            const needle = this.marginTypes[key];
            needle.partner_cashback_rules = needle.partner_cashback_rules ? [...needle.partner_cashback_rules, {threshold: 0, value: 0}] : [{threshold: 0, value: 0}];
            this.$set(this.marginTypes, key, needle)
        },
        addSalaryRule(key) {
            const needle = this.marginTypes[key];
            needle.salary_rules = needle.salary_rules ? [...needle.salary_rules, {threshold: 0, value: 0}] : [{threshold: 0, value: 0}];
            this.$set(this.marginTypes, key, needle)
        },
        deleteCashbackRule(typeKey, ruleKey) {
            const needle = this.marginTypes[typeKey];
            needle.partner_cashback_rules.splice(ruleKey, 1);
            this.$set(this.marginTypes, typeKey, needle)
        },
        deleteSalaryRule(typeKey, ruleKey) {
            const needle = this.marginTypes[typeKey];
            needle.salary_rules.splice(ruleKey, 1);
            this.$set(this.marginTypes, typeKey, needle)
        },
        async onSave () {
            await this.$store.dispatch(ACTIONS.UPDATE_MARGIN_TYPES, this.marginTypes);
            this.$toast.success('Данные сохранены!')
        },
    }
}
</script>
