<template>
    <daisy-modal
        :open="true"
        :showCloseButton="false"
        :closeable="false"
        class="h-full w-full bg-transparent border-none flex flex-col justify-center items-center shadow-none"
    >
        <div
            v-if="!templateBuilderStep"
            class="max-w-5xl min-w-[25rem] mx-auto mt-48"
        >
            <h2 class="text-2xl">Select a template</h2>
            <div class="mt-4">
                <div class="border-b border-base-content flex just pb-2">
                    <p class="text-secondary">Layouts</p>
                    <p class="ml-8">Saved Templates</p>

                    <div class="ml-auto">
                        <p class="pr-8">Search</p>
                    </div>
                </div>
            </div>

            <h2 class="text-xl pt-4 pb-6">Featured</h2>

            <div class="w-full grid grid-cols-[0.75fr,0.25fr] gap-4">
                <!-- slectable -->
                <div
                    class="bg-black border-secondary border w-full h-72 px-4 rounded-md flex items-center overflow-x-scroll overflow-y-hidden"
                    :class="{ 'justify-center': templates.length === 1 }"
                    ref="templateScrollContainer"
                >
                    <TemplatePreview
                        v-for="t in templates"
                        :title="t.name"
                        :temp_id="t.id"
                        :selected="t.id === selectedTemplate"
                        :thumbsrc="t.thumbnail?.url"
                        :template_type="template_type"
                        :template_item="t"
                        @submit="updateSelected"
                    />
                </div>

                <!-- create new -->
                <button
                    @click="handleNewTemplate"
                    class="bg-black border-secondary border w-full h-72 rounded-md flex justify-center items-center flex-col"
                >
                    <div
                        class="border-2 border-secondary hover:border-opacity-25 transition-all duration-500 rounded-full h-20 w-20 flex justify-center items-center"
                    >
                        <span class="text-7xl pb-1">+</span>
                    </div>
                    <p class="mt-2">Create New</p>
                </button>
            </div>

            <button
                @click="$emit('save', selectedTemplate)"
                class="px-2 py-1 mt-8 mx-auto block border-secondary hover:bg-secondary transition-all border rounded-md"
            >
                Save
            </button>
        </div>

        <div
            v-if="templateBuilderStep"
            class="mx-auto mt-48 bg-black border-secondary border rounded-md p-4 w-max"
        >
            <email-template-form
                v-if="template_type === 'email'"
                :can-activate="false"
                :topol-api-key="topolApiKey"
                :use-inertia="false"
                @done="handleEmailTemplateDone"
            />
            <sms-template-form
                v-if="template_type === 'sms'"
                :can-activate="false"
                :use-inertia="false"
                @done="handleSmsTemplateDone"
            />

            <call-script
                v-if="template_type === 'call'"
                @save="handleCallTemplateDone"
                @cancel="handleCancel"
            />
            <!-- <h2 class="text-2xl text-center">Email Template Form</h2> -->
            <!-- <p class="text-lg font-bold text-center">should be open here</p> -->
        </div>

        <!-- <button
            @click="$emit('close')"
            aria-hidden="true"
            class="absolute top-0 left-0 h-full w-full z-[-1] cursor-default"
        ></button> -->
    </daisy-modal>
</template>

<script setup>
import { ref } from "vue";
import { faPlus } from "@fortawesome/pro-solid-svg-icons";
import { library } from "@fortawesome/fontawesome-svg-core";
import { Inertia } from "@inertiajs/inertia";
import DaisyModal from "@/Components/DaisyModal.vue";
import TemplatePreview from "./Templates/TemplatePreview.vue";
import EmailTemplateForm from "@/Pages/Comms/Emails/Templates/Partials/EmailTemplateForm.vue";
import SmsTemplateForm from "@/Pages/Comms/SMS/Templates/Partials/SmsTemplateForm.vue";
import CallScript from "./Creator/CallScript.vue";

library.add(faPlus);

const templateBuilderStep = ref(false);

const props = defineProps({
    selected: {
        type: [Number, String, null],
        default: null,
    },
    topolApiKey: {
        type: String,
        required: true,
    },
    templates: {
        type: Array,
        default: [],
    },
    template_type: {
        type: [String, undefined],
        default: undefined,
    },
});

const selectedTemplate = ref(props.selected);

const updateSelected = (t) => {
    console.log("update selected template to id:", t);
    selectedTemplate.value = t;
};

const handleNewTemplate = () => {
    templateBuilderStep.value = true;
    console.log("create new clicked");
};

const handleEmailTemplateDone = (template) => {
    console.log("handleEmailTemplateDone", template);
    Inertia.reload({
        only: ["email_templates"],
        onFinish: () => {
            templateBuilderStep.value = false;
            updateSelected(template.id);
            console.log({
                templateScrollContainer: templateScrollContainer.value,
            });
            //TODO: animate this
            templateScrollContainer.value.scrollLeft = document.getElementById(
                template.id
            ).offsetLeft;
        },
    });
};

const handleSmsTemplateDone = (template) => {
    console.log("handleSmsTemplateDone", template);
    Inertia.reload({
        only: ["sms_templates"],
        onFinish: () => {
            templateBuilderStep.value = false;
            updateSelected(template.id);
            console.log({
                templateScrollContainer: templateScrollContainer.value,
            });
            //TODO: animate this
            templateScrollContainer.value.scrollLeft =
                document.getElementById(template.id)?.offsetLeft || 0;
        },
    });
};

const handleCallTemplateDone = (template) => {
    console.log("handlecallTemplateDone", template);
    Inertia.reload({
        only: ["call_templates"],
        onFinish: () => {
            templateBuilderStep.value = false;
            updateSelected(template.id);
            console.log({
                templateScrollContainer: templateScrollContainer.value,
            });
            //TODO: animate this
            templateScrollContainer.value.scrollLeft =
                document.getElementById(template.id)?.offsetLeft || 0;
        },
    });
};

const handleCancel = () => {
    templateBuilderStep.value = false;
};

const templateScrollContainer = ref(null);
</script>
