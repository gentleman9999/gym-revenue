<template>
    <jet-form-section @submitted="handleSubmit">
        <template #form>
            <div class="col-span-6">
                <jet-label for="filename" value="Sharing options for " />
                <span
                    :href="item.url"
                    :download="item.filename"
                    target="_blank"
                    class="link link-hover"
                >
                    {{ item.name }}
                </span>
                <jet-input-error :message="form.errors.item" class="mt-2" />
            </div>

            <div
                class="form-control"
                v-for="option in options"
                :key="option.key"
            >
                <span>{{ option.label }}</span>
                <multiselect
                    :id="option.key"
                    v-model="form.sharing[option.key]"
                    class="bg-neutral text-neutral py-2"
                    mode="tags"
                    :close-on-select="false"
                    :create-option="true"
                    :options="option.options"
                    :classes="multiselectClasses"
                    :searchable="true"
                />
            </div>
            <jet-input-error :message="form.errors.sharing" class="mt-2" />
        </template>

        <template #actions>
            <Button
                class="btn-secondary"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                :loading="form.processing"
            >
                Update
            </Button>
        </template>
    </jet-form-section>
</template>
<style>
.focus\:shadow-none:focus {
    box-shadow: none !important;
}
</style>
<script setup>
import { defineEmits, computed } from "vue";

import { usePage } from "@inertiajs/inertia-vue3";
import { getDefaultMultiselectTWClasses, useGymRevForm } from "@/utils";

import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";

import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import { Inertia } from "@inertiajs/inertia";
import Multiselect from "@vueform/multiselect";

const page = usePage();

const props = defineProps({
    item: {
        type: Object,
    },
});

const emit = defineEmits(["submitted"]);

let urlPrev = usePage().props.value.urlPrev;
const form = useGymRevForm({
    sharing: {
        user_ids: props.item?.user_ids || [],
        department_ids: props.item?.department_ids || [],
        position_ids: props.item?.position_ids || [],
        location_ids: props.item?.location_ids || [],
        role_ids: props.item?.role_ids || [],
        team_ids: props.item?.team_ids || [],
    },
});

let handleSubmit = async () => {
    await Inertia.put(route("folders.sharing.update", props.item.id), {
        ...form.sharing,
    });
    emit("success");
};

const options = [
    {
        key: "user_ids",
        label: "Select users to share with",
        options: page.props.value.users.map((item) => ({
            label: item.name,
            value: item.id,
        })),
    },
    {
        key: "department_ids",
        label: "Select Departments to share with",
        options: page.props.value.departments.map((item) => ({
            label: item.name,
            value: item.id,
        })),
    },
    {
        key: "position_ids",
        label: "Select Positions to share with",
        options: page.props.value.positions.map((item) => ({
            label: item.name,
            value: item.id,
        })),
    },
    {
        key: "location_ids",
        label: "Select Locations to share with",
        options: page.props.value.locations.map((item) => ({
            label: item.name,
            value: item.id,
        })),
    },
    {
        key: "role_ids",
        label: "Select Roles to share with",
        options: page.props.value.roles.map((item) => ({
            label: item.name,
            value: item.id,
        })),
    },
    {
        key: "team_ids",
        label: "Select Teams to share with",
        options: page.props.value.teams.map((item) => ({
            label: item.name,
            value: item.id,
        })),
    },
];

const multiselectClasses = {
    ...getDefaultMultiselectTWClasses(),
    dropdown:
        "max-h-60 absolute -left-px -right-px bottom-0 transform translate-y-full border border-gray-300 -mt-px overflow-y-scroll z-50 bg-base-content text-base-100 flex flex-col rounded-b",
};
</script>

<style scoped>
.form-control {
    @apply col-span-6 flex flex-col gap-4;
}
</style>
