<template>
    <div
        v-if="!templateBuilderStep && !confirmingTrash"
        class="min-w-[25rem] w-full px-20"
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
                :class="{
                    'justify-center': isLoadingList,
                    'justify-start':
                        !isLoadingList && !modelLoading && !!resources,
                }"
                class="bg-black border-secondary border w-full px-4 rounded-md flex items-center overflow-x-scroll overflow-y-hidden scroll-smooth"
                ref="templateScrollContainer"
            >
                <template v-if="(isLoadingList || modelLoading) && !result">
                    <Spinner />
                </template>

                <template v-else-if="!isLoadingList && !!resources">
                    <TemplatePreview
                        v-for="t in resources.data"
                        :title="t.name"
                        :temp_id="t.id"
                        :selected="t.id === selectedTemplate"
                        :thumbsrc="t.thumbnail?.url"
                        :template_type="template_type"
                        :template_item="t"
                        :permissions="permissions"
                        @submit="updateSelected"
                        @edit="handleEditTemplate"
                        @trash="handleConfirmTrash"
                    />
                </template>
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

        <div class="flex justify-between items-center mt-6">
            <button
                class="selector-btn bg-neutral hover:bg-neutral-content hover:bg-opacity-25 active:opacity-50"
                @click="$emit('cancel')"
            >
                Cancel
            </button>

            <button
                @click="$emit('save', selectedTemplate)"
                class="selector-btn bg-primary hover:bg-secondary active:opacity-50"
            >
                Save
            </button>

            <span></span>
        </div>
    </div>

    <template v-if="confirmingTrash">
        <div
            class="bg-neutral border-primary border-2 px-16 py-8 z-10 absolute rounded-md"
        >
            <p class="text-xl">Really trash template?</p>
            <div class="flex gap-8 items-center justify-center mt-8">
                <button
                    @click="handleCancelConfirmTrash"
                    class="selector-btn bg-neutral hover:bg-neutral-content hover:bg-opacity-25 active:opacity-25"
                >
                    Cancel
                </button>
                <button
                    @click="requestTrash"
                    class="selector-btn bg-secondary hover:bg-primary active:opacity-50"
                >
                    Confirm
                </button>
            </div>
        </div>
    </template>

    <div
        v-if="templateBuilderStep && template_type === 'email'"
        class="scale-90 bg-neutral border-primary border-2 rounded-md p-4"
    >
        <email-template-form
            :template="editingTemplate"
            :editParam="editParam"
            :createParam="emailCreateParam"
            @done="handleBuilderDone"
            @cancel="handleCancelBuilder"
        />
    </div>

    <!-- SMS Create -->
    <div
        v-if="
            templateBuilderStep &&
            template_type === 'sms' &&
            builderOperation === 'create'
        "
        class="bg-neutral"
    >
        <sms-template-form
            @done="handleBuilderDone"
            @cancel="handleCancelBuilder"
        />
    </div>

    <!-- SMS Edit -->
    <div
        v-if="
            templateBuilderStep &&
            template_type === 'sms' &&
            builderOperation === 'edit'
        "
        class="bg-neutral"
    >
        <ApolloQuery
            :query="(gql) => queries.smsTemplate[builderOperation]"
            :variables="editParam"
        >
            <template v-slot="{ result: { data, loading, error }, isLoading }">
                <template v-if="isLoading">
                    <div class="shadow border border-secondary rounded-lg p-6">
                        <Spinner />
                    </div>
                </template>

                <sms-template-form
                    v-if="!isLoading && !!data"
                    :editParam="editParam"
                    :template="data['smsTemplate']"
                    @done="handleBuilderDone"
                    @cancel="handleCancelBuilder"
                />
            </template>
        </ApolloQuery>
    </div>

    <!-- Call script Create -->
    <call-script
        v-if="
            templateBuilderStep &&
            template_type === 'call' &&
            builderOperation === 'create'
        "
        @done="handleBuilderDone"
        @cancel="handleCancelBuilder"
    />

    <!-- Call script Edit -->
    <template
        v-if="
            templateBuilderStep &&
            template_type === 'call' &&
            builderOperation === 'edit'
        "
    >
        <ApolloQuery
            :query="(gql) => queries.callTemplate[builderOperation]"
            :variables="editParam"
        >
            <template v-slot="{ result: { data, loading, error }, isLoading }">
                <template v-if="isLoading">
                    <div class="shadow border border-secondary rounded-lg p-6">
                        <Spinner />
                    </div>
                </template>

                <call-script
                    v-if="!isLoading && !!data"
                    :editParam="editParam"
                    :template="data['callTemplate']"
                    @done="handleBuilderDone"
                    @cancel="handleCancelBuilder"
                />
            </template>
        </ApolloQuery>
    </template>
</template>

<style scoped>
.selector-btn {
    @apply px-2 py-1 block transition-all rounded-md border border-transparent;
}
</style>

<script setup>
import axios from "axios";
import { ref, computed, watch } from "vue";
import queries from "@/gql/queries";
import { useQuery } from "@vue/apollo-composable";
import { faPlus } from "@fortawesome/pro-solid-svg-icons";
import { library } from "@fortawesome/fontawesome-svg-core";
import { resolveTemplateType } from "@/Pages/Templates/components/helpers";
import { Inertia } from "@inertiajs/inertia";
import { usePage } from "@inertiajs/inertia-vue3";
import { toastSuccess, toastError, toastInfo } from "@/utils/createToast";

import Spinner from "@/Components/Spinner.vue";
import TemplatePreview from "./Templates/TemplatePreview.vue";
import EmailTemplateForm from "@/Pages/Comms/Emails/Templates/Partials/EmailTemplateForm.vue";
import SmsTemplateForm from "@/Pages/Comms/SMS/Templates/Partials/SmsTemplateForm.vue";
import CallScript from "./Creator/CallScript.vue";

library.add(faPlus);

const emit = defineEmits(["save", "cancel"]);
const templateBuilderStep = ref(false);

const props = defineProps({
    selected: {
        type: [Number, String, Boolean, null],
        default: null,
    },
    template_type: {
        type: String,
        required: true,
    },
});

const param = ref({
    page: 1,
});

const editParam = ref(null);
const builderOperation = ref(null);

/**
 * Params for forms to determine their operations
 */

const isLoadingList = ref(true);

const {
    result,
    loading: modelLoading,
    error,
    refetch,
} = useQuery(
    queries[`${props.template_type}Templates`],
    props.param ? props.param : param,
    { throttle: 500 }
);

const resources = computed(() => {
    if (result.value && result.value[`${props.template_type}Templates`]) {
        return _.cloneDeep(result.value[`${props.template_type}Templates`]);
    } else return null;
});

watch(modelLoading, (nv, ov) => {
    console.log("resources:", resources);
    if (!!resources?.value) {
        isLoadingList.value = false;
    }
});

const templateType = ref(props.template_type);
const selectedTemplate = ref(props.selected);
const editingTemplate = ref(null);
const confirmingTrash = ref(null);

const updateSelected = (t) => {
    selectedTemplate.value = t;
};

const handleNewTemplate = () => {
    builderOperation.value = "create";
    templateBuilderStep.value = true;
};

const handleEditTemplate = (templateId) => {
    builderOperation.value = "edit";
    editParam.value = {
        id: templateId,
    };
    templateBuilderStep.value = true;
};

// when cancel is called from the editor
const handleCancelBuilder = () => {
    builderOperation.value = null;
    editParam.value = null;
    templateBuilderStep.value = false;
};

const handleConfirmTrash = (templateId) => {
    confirmingTrash.value = templateId;
};

const handleCancelConfirmTrash = () => {
    confirmingTrash.value = null;
};

// when create/edit operation is successful from the builder
const handleBuilderDone = async (id) => {
    console.log("builderDone called", id);

    if (typeof id === "object") updateSelected(id?.id);
    else updateSelected(id);

    templateBuilderStep.value = false;
    builderOperation.value = null;

    editingTemplate.value = null;
    isLoadingList.value = true;
    await refetch();
};

/**
 * Called when finished editing or creating a new template
 * closes the template builder, shows template selector again
 * and scrolls to the newly created / edited template
 * @param {object} template - new template data received from server
 */
// const actionDone = (template) => {
//     let templateType = resolveTemplateType(template);
//     let refreshProp = templateType?.toLocaleLowerCase() + "_templates";

//     /** email builder takes care of it's own notifications */
//     if (templateType !== "email") {
//         toastSuccess(templateType + " Template Saved!");
//     }

//     if (template.reuse) {
//         emit("save", selectedTemplate.value);
//     }

//     // Inertia.reload({
//     //     only: [refreshProp],
//     //     onFinish: () => {
//     //         templateBuilderStep.value = false;
//     //         updateSelected(template.id);
//     //         document
//     //             .getElementById(template.id)
//     //             .scrollIntoView({ block: "center" });
//     //     },
//     // });
// };

/**
 * Sends a request to trash the template with the passed id to the back end
 */
const requestTrash = async () => {
    let templateId = confirmingTrash.value;
    let refreshProp = props.template_type + "_templates";
    console.log("id", templateId);

    try {
        // const ep = "mass-comms." + props.template_type + "-templates.trash";
        // console.log("ep", ep);
        // const res = await axios.delete(route(ep, templateId));
        // if (res.status === 200) {
        //     toastInfo("Template Trashed");
        //     confirmingTrash.value = null;
        //     Inertia.reload({
        //         only: [refreshProp],
        //     });
        // }
    } catch (error) {
        console.log("error", error);
        confirmingTrash.value = null;
        toastError("There was a problem trashing this template.");
    }
};

const templateScrollContainer = ref(null);

/** Actions the current user can perform (affects layout) */
const permissions = computed(() => {
    let page = usePage();
    let user = page.props.value.user;

    let isAdmin = user?.abilities?.includes("*");

    const checkPermission = (prefix, suffix) => {
        if (isAdmin) return true;
        return user?.abilities?.includes(`${prefix}-templates.${suffix}`);
    };

    return {
        email: {
            create: checkPermission("email", "create"),
            update: checkPermission("email", "update"),
            trash: checkPermission("email", "trash"),
            restore: checkPermission("email", "restore"),
            read: checkPermission("email", "read"),
            delete: checkPermission("email", "delete"),
        },
        call: {
            create: checkPermission("call", "create"),
            update: checkPermission("call", "update"),
            trash: checkPermission("call", "trash"),
            restore: checkPermission("call", "restore"),
            read: checkPermission("call", "read"),
            delete: checkPermission("call", "delete"),
        },
        sms: {
            create: checkPermission("sms", "create"),
            update: checkPermission("sms", "update"),
            trash: checkPermission("sms", "trash"),
            restore: checkPermission("sms", "restore"),
            read: checkPermission("sms", "read"),
            delete: checkPermission("sms", "delete"),
        },
    };
});
</script>
