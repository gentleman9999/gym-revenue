<template>
    <app-layout title="Lead Sources">
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">Lead Sources</h2>
        </template>

        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="max-w-md space-y-2">
                <div v-for="(source, index) in sources">
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
    </app-layout>
</template>

<script>
import { defineComponent, ref } from "vue";
import { useForm } from "@inertiajs/inertia-vue3";
import AppLayout from "@/Layouts/AppLayout";
import JetSectionBorder from "@/Jetstream/SectionBorder";
import Button from "@/Components/Button";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faPlus } from "@fortawesome/pro-solid-svg-icons";
library.add(faPlus);

export default defineComponent({
    props: ["sources"],

    components: {
        AppLayout,
        JetSectionBorder,
        Button,
        FontAwesomeIcon,
    },
    setup(props) {
        const inputs = ref([]);
        const setItemRef = (el) => {
            if (el) {
                inputs.value.push(el);
            }
        };
        const form = useForm({ sources: props.sources });
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
            form.post(route("data.leads.sources.update"));
        };
        return { form, addNewSource, inputs, setItemRef, submitForm };
    },
});
</script>
