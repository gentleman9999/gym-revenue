<template>
    <app-layout title="Lead Statuses">
        <page-toolbar-nav title="Lead Statuses" :links="navLinks" />

        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="max-w-md space-y-2">
                <div v-for="(source, index) in form.statuses">
                    <input
                        type="text"
                        v-model="form.statuses[index].status"
                        :ref="setItemRef"
                        class="w-full"
                    />
                </div>
                <div class="flex flex-row justify-center py-2">
                    <button type="button" @click="addNewStatus">
                        <font-awesome-icon
                            icon="plus"
                            size="2x"
                            class="opacity-50 hover:opacity-100 transition-opacity"
                        />
                    </button>
                </div>
                <jet-section-border />
                <div class="flex flex-row">
                    <Button
                        type="button"
                        @click="$inertia.visit(route('data.leads'))"
                        :class="{ 'opacity-25': form.processing }"
                        error
                        outline
                        :disabled="form.processing"
                    >
                        Cancel
                    </Button>
                    <div class="flex-grow" />
                    <Button
                        :class="{ 'opacity-25': form.processing }"
                        class="btn-primary"
                        :disabled="form.processing || !form.isDirty"
                        :loading="form.processing"
                        type="button"
                        @click="submitForm"
                    >
                        Save
                    </Button>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import { defineComponent, ref } from "vue";
import { comingSoon } from "@/utils/comingSoon.js";
import { useGymRevForm } from "@/utils";
import AppLayout from "@/Layouts/AppLayout";
import JetSectionBorder from "@/Jetstream/SectionBorder";
import Button from "@/Components/Button";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faPlus } from "@fortawesome/pro-solid-svg-icons";
import PageToolbarNav from "@/Components/PageToolbarNav";
library.add(faPlus);
export default defineComponent({
    props: ["statuses"],

    components: {
        AppLayout,
        JetSectionBorder,
        Button,
        FontAwesomeIcon,
        PageToolbarNav,
    },
    setup(props) {
        const inputs = ref([]);
        const setItemRef = (el) => {
            if (el) {
                inputs.value.push(el);
            }
        };
        const form = useGymRevForm({ statuses: props.statuses });
        const addNewStatus = () => {
            if (form.statuses[form.statuses.length - 1].status === "") {
                focusLastInput();
                return;
            }
            form.statuses.push({ id: null, status: "" });
            setTimeout(focusLastInput, 100);
        };

        const focusLastInput = () => {
            inputs.value[inputs.value.length - 1].focus();
        };

        const submitForm = () => {
            console.log("submitform", form.data());
            form.dirty().post(route("data.leads.statuses.update"));
        };

        const navLinks = [
            {
                label: "Dashboard",
                href: "#",
                onClick: comingSoon,
                active: false,
            },
            {
                label: "Calendar",
                href: "#",
                onClick: comingSoon,
                active: false,
            },
            {
                label: "Leads",
                href: "#",
                onClick: comingSoon,
                active: false,
            },
            {
                label: "Tasks",
                href: "#",
                onClick: comingSoon,
                active: false,
            },
            {
                label: "Contacts",
                href: "#",
                onClick: comingSoon,
                active: false,
            },
            {
                label: "Consultants",
                href: "#",
                onClick: comingSoon,
                active: false,
            },
            {
                label: "Lead Sources",
                href: route("data.leads.sources"),
                onClick: null,
                active: false,
            },
            {
                label: "Lead Statuses",
                href: route("data.leads.statuses"),
                onClick: null,
                active: true,
            },
        ];

        return { form, addNewStatus, inputs, setItemRef, submitForm, navLinks };
    },
});
</script>

<style scoped></style>
