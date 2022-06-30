<template>
    <div class="flex flex-row nowrap items-center gap-4 truncate">
        <input id="name" class="" value="" />
        <input id="description" class="" value="" />
        <input id="remind_time" class="" value="" />
        <input id="triggered_at" class="" value="" />
    </div>
</template>

<script>
import AutoDataCard from "@/Components/CRUD/AutoDataCard";
import ReminderIcon from "./ReminderIcon";

export default {
    components: {
        ReminderIcon,
    },
    props: {
        reminder: {
            type: Object,
            required: true,
        },
        fields: {
            type: Array,
        },
        modelName: {
            type: String,
            default: "record",
        },
        modelNamePlural: {
            type: String,
        },
        titleField: {
            type: String,
        },
        actions: {
            type: Object,
            default: {},
        },
    },
};
</script>
