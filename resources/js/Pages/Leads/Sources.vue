<template>
    <LayoutHeader title="Lead Sources">
        <h2 class="font-semibold text-xl leading-tight">Lead Sources</h2>
    </LayoutHeader>
    <page-toolbar-nav title="Lead Sources" :links="navLinks" />

    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <div class="max-w-md space-y-2">
            <div v-for="(source, index) in form.sources">
                <input
                    type="text"
                    v-model="form.sources[index].name"
                    :ref="setItemRef"
                    class="w-full"
                />
            </div>
            <div class="flex flex-row justify-center py-2">
                <button type="button" @click="addNewSource">
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
                    :disabled="form.processing"
                    :loading="form.processing"
                    type="button"
                    @click="submitForm"
                >
                    Save
                </Button>
            </div>
        </div>
    </div>
</template>

<script>
import { defineComponent, ref } from "vue";
import { comingSoon } from "@/utils/comingSoon.js";
import { useGymRevForm } from "@/utils";
import LayoutHeader from "@/Layouts/LayoutHeader";
import JetSectionBorder from "@/Jetstream/SectionBorder";
import Button from "@/Components/Button";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faPlus } from "@fortawesome/pro-solid-svg-icons";
import PageToolbarNav from "@/Components/PageToolbarNav";
library.add(faPlus);

export default defineComponent({
    props: ["sources"],

    components: {
        LayoutHeader,
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
        const form = useGymRevForm({ sources: props.sources });
        const addNewSource = () => {
            if (form.sources[form.sources.length - 1].name === "") {
                focusLastInput();
                return;
            }
            form.sources.push({ id: null, name: "" });
            setTimeout(focusLastInput, 100);
        };

        const focusLastInput = () => {
            inputs.value[inputs.value.length - 1].focus();
        };

        const submitForm = () => {
            console.log("submitform", form.data());
            form.dirty().post(route("data.leads.sources.update"));
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
                active: true,
            },
            {
                label: "Lead Statuses",
                href: route("data.leads.statuses"),
                onClick: null,
                active: false,
            },
        ];

        return { form, addNewSource, inputs, setItemRef, submitForm, navLinks };
    },
});
</script>
