<template>
    <tbody>
        <tr
            v-for="item in props.data"
            :key="item.id"
            class="hover:bg-indigo-100"
        >
            <td class="border-l border-secondary">
                <div>{{ item.location }}</div>
            </td>
            <td>
                <div>{{ item.event }}</div>
            </td>
            <td>
                <div><img :src="item.employee" /></div>
            </td>
            <td>
                <div>{{ item.time }}</div>
            </td>
            <td>
                <select-box
                    label="status"
                    :items="available_status"
                    size="xs"
                    class="float-right"
                />
            </td>
            <td class="border-r border-secondary">
                <Button secondary size="xs">View</Button>
            </td>
        </tr>
    </tbody>
</template>
<style scoped>
td {
    border-bottom: 1px solid #0074c7;
    border-top: 1px solid #0074c7;
    height: 1px;
    padding: 12px 0;
}
td:not(:nth-last-child(2)) > div {
    align-items: center;
    border-right: 1px solid #0074c7;
    justify-content: center;
    display: flex;
    height: 100%;
    text-align: center;
}
</style>
<script setup>
import SelectBox from "@/Components/SelectBox";
import Button from "@/Components/Button";
const props = defineProps({
    data: {
        type: Array,
        default: [],
    },
});
const available_status = [
    "Done",
    "To Do",
    "Reschedule",
    "Confirmed",
    "Show",
    "No Show",
];
</script>
