<template>
    <check-in-card class="space-y-3 px-3 py-6">
        <account-form-element
            v-for="item in data"
            :key="item.id"
            :label="item.label"
            :value="form[item.id]"
            :required="item.required"
            :readonly="item.readonly"
        />
    </check-in-card>
</template>
<script setup>
import CheckInCard from "../../components/check-in-card.vue";
import { useGymRevForm } from "@/utils";
import AccountFormElement from "./account-form-element.vue";
const form = useGymRevForm({
    username: "kevinbuchanan@email.com",
});

const data = [
    {
        id: "first_name",
        label: "First Name",
        required: true,
    },
    {
        id: "middle_name",
        label: "Middle Name",
    },
    {
        id: "last_name",
        label: "Last Name",
    },
    {
        id: "birthday",
        label: "Date of Birth",
    },
    {
        id: "gender",
        label: "Gender",
    },
    {
        id: "username",
        label: "Username",
        readonly: true,
    },
    {
        id: "mobile",
        label: "Mobile",
    },
    {
        id: "address",
        label: "Address",
    },
    {
        id: "city",
        label: "City",
    },
    {
        id: "state",
        label: "State",
    },
    {
        id: "zip",
        label: "Zip Code",
    },
];
</script>
