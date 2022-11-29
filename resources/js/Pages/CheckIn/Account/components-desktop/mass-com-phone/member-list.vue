<template>
    <div class="member-list-actions">
        <Button outline secondary size="xs" @click="$emit('toggle-mode')"
            >Members</Button
        >
        <Button outline size="xs" @click="$emit('toggle-mode')">Search</Button>
    </div>
    <div class="flex flex-col">
        <data-table
            :collapsed="true"
            :columns="columns"
            :data="data"
            class="h-[500px]"
        ></data-table>
        <Button
            ghost
            size="xs"
            class="self-end mt-6"
            @click="$emit('toggle-mode')"
            >Cancel</Button
        >
    </div>
</template>
<style scoped>
.member-list-actions {
    @apply flex flex-row justify-between mb-6;
}
</style>
<script setup>
import { h } from "vue";
import Button from "@/Components/Button.vue";
import DataTable from "@/Components/DataTable";
const columns = [
    {
        field: "avatar",
        label: "",
        renderer: (value) => {
            return h("img", { src: value });
        },
    },
    {
        field: "name",
        label: "Name",
    },
    {
        field: "location",
        label: "Location",
    },
    {
        field: "",
        label: "",
        renderer: () => {
            return h("div", ["..."]);
        },
    },
];
const data = [
    {
        name: "Chad Hodges",
        location: "Club location",
        avatar: "/img/avatars/chad.png",
    },
    {
        name: "Don Torres",
        location: "Club location",
        avatar: "/img/avatars/don.png",
    },
    {
        name: "Philip Nash",
        location: "Club location",
        avatar: "/img/avatars/philip.png",
    },
    {
        name: "Stephen Hicks",
        location: "Club location",
        avatar: "/img/avatars/stephen.png",
    },
    {
        name: "Chad Hodges",
        location: "Club location",
        avatar: "/img/avatars/chad.png",
    },
    {
        name: "Don Torres",
        location: "Club location",
        avatar: "/img/avatars/don.png",
    },
    {
        name: "Philip Nash",
        location: "Club location",
        avatar: "/img/avatars/philip.png",
    },
    {
        name: "Stephen Hicks",
        location: "Club location",
        avatar: "/img/avatars/stephen.png",
    },
    {
        name: "Chad Hodges",
        location: "Club location",
        avatar: "/img/avatars/chad.png",
    },
    {
        name: "Don Torres",
        location: "Club location",
        avatar: "/img/avatars/don.png",
    },
    {
        name: "Philip Nash",
        location: "Club location",
        avatar: "/img/avatars/philip.png",
    },
    {
        name: "Stephen Hicks",
        location: "Club location",
        avatar: "/img/avatars/stephen.png",
    },
];
</script>
