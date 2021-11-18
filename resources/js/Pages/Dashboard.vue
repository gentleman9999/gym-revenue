<template>
    <app-layout>
        <template #header>
            Dashboard
        </template>

        <jet-bar-container>
            <jet-bar-alert text="This is an alert message" />

            <jet-bar-stats-container >
                <jet-bar-stat-card title="Total Clients" :number="clients.length" type="warning">
                    <template v-slot:icon>
                        <jet-bar-icon type="users" fill />
                    </template>
                </jet-bar-stat-card>

                <jet-bar-stat-card title="Total Revenue Funneled" number="$ 0" type="success">
                    <template v-slot:icon>
                        <jet-bar-icon type="money" fill />
                    </template>
                </jet-bar-stat-card>

                <jet-bar-stat-card title="Total Profits" number="$ 0" type="info">
                    <template v-slot:icon>
                        <jet-bar-icon type="cart" fill />
                    </template>
                </jet-bar-stat-card>

                <jet-bar-stat-card title="Total MCU Films" number="26" type="danger">
                    <template v-slot:icon>
                        <jet-bar-icon type="message" fill />
                    </template>
                </jet-bar-stat-card>
            </jet-bar-stats-container>

            <jet-bar-table :headers="['client', 'status', 'joined', '', '']" >
                <tr class="hover:bg-base-100" v-for="client in clients" :key="client.id">
                    <jet-bar-table-data>{{ client.name }}</jet-bar-table-data>
                    <jet-bar-table-data>
                        <jet-bar-badge text="Active" type="success" v-if="client.active"/>
                        <jet-bar-badge text="Not Active" type="danger" v-else/>
                    </jet-bar-table-data>
                    <jet-bar-table-data>{{ client.created_at}} </jet-bar-table-data>
                    <jet-bar-table-data>
                        <inertia-link href="#" class="text-indigo-600 hover:text-indigo-900">Edit</inertia-link>
                    </jet-bar-table-data>
                    <jet-bar-table-data>
                        <inertia-link href="#" class="text-gray-400 hover:text-gray-500">
                            <jet-bar-icon type="trash" fill />
                        </inertia-link>
                    </jet-bar-table-data>
                </tr>
            </jet-bar-table>

        </jet-bar-container>

    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import JetBarContainer from "@/Components/JetBarContainer";
import JetBarAlert from "@/Components/JetBarAlert";
import JetBarStatsContainer from "@/Components/JetBarStatsContainer";
import JetBarStatCard from "@/Components/JetBarStatCard";
import JetBarTable from "@/Components/JetBarTable";
import JetBarTableData from "@/Components/JetBarTableData";
import JetBarBadge from "@/Components/JetBarBadge";
import JetBarIcon from "@/Components/JetBarIcon";

export default {
    components: {
        AppLayout,
        JetBarContainer,
        JetBarAlert,
        JetBarStatsContainer,
        JetBarStatCard,
        JetBarTable,
        JetBarTableData,
        JetBarBadge,
        JetBarIcon,
    },
    props: [
        'clients'
    ],
    data() {},
    methods: {},
    computed: {},
    mounted() {
        console.log('GymRevenue Dashboard');
    }
}
</script>
