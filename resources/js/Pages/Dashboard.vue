<template>
    <app-layout>
        <template #header>
            <div class="flex flex-row justify-between">
                <div><p>Dashboard</p></div>
                <div><p>{{ accountName }}</p></div>
            </div>

        </template>

        <jet-bar-container>
            <!-- @todo - leave this here and make it contextual, dynamic, pusher-enabled? -->
            <!-- <jet-bar-alert text="This is an alert message" /> -->

            <jet-bar-stats-container>
                <jet-bar-stat-card v-for="(widget, idx) in widgets" :title="widget.title" :number="widget.value" :type="widget.type">
                    <template v-slot:icon>
                        <jet-bar-icon :type="widget.icon" fill />
                    </template>
                </jet-bar-stat-card>

                <!--
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
                -->
            </jet-bar-stats-container>

            <gym-revenue-table :headers="['client', 'status', 'joined', '', '']" >
                <tr class="hover" v-for="client in clients" :key="client.id">
                    <td>{{ client.name }}</td>
                    <td>
                        <jet-bar-badge text="Active" type="success" v-if="client.active"/>
                        <jet-bar-badge text="Not Active" type="danger" v-else/>
                    </td>
                    <td>{{ client.created_at}} </td>
                    <td>
                        <inertia-link href="#" class="">Edit</inertia-link>
                    </td>
                    <td>
                        <inertia-link href="#" class=" hover:">
                            <jet-bar-icon type="trash" fill />
                        </inertia-link>
                    </td>
                </tr>
            </gym-revenue-table>

        </jet-bar-container>

    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import JetBarContainer from "@/Components/JetBarContainer";
import JetBarAlert from "@/Components/JetBarAlert";
import JetBarStatsContainer from "@/Components/JetBarStatsContainer";
import JetBarStatCard from "@/Components/JetBarStatCard";
import GymRevenueTable from "@/Components/GymRevenueTable";
import JetBarBadge from "@/Components/JetBarBadge";
import JetBarIcon from "@/Components/JetBarIcon";

export default {
    components: {
        AppLayout,
        JetBarContainer,
        JetBarAlert,
        JetBarStatsContainer,
        JetBarStatCard,
        GymRevenueTable,
        JetBarBadge,
        JetBarIcon,
    },
    props: [
        'clients',
        'accountName',
        'widgets'
    ],
    data() {},
    methods: {},
    computed: {},
    mounted() {
        console.log('GymRevenue Dashboard');
    }
}
</script>
