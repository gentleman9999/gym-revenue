<template>
    <daisy-modal
        :open="true"
        :showCloseButton="false"
        :closable="false"
        class="h-full w-full bg-transparent border-none flex flex-col justify-center items-center shadow-none"
    >
        <template v-if="!!isProcessing && !!loading">
            <div
                class="shadow border border-secondary rounded-lg p-6 bg-neutral"
            >
                <Spinner />
            </div>
        </template>

        <template v-else>
            <div
                class="border border-secondary bg-black p-4 rounded-md max-w-sm w-full"
            >
                <div class="flex my-2">
                    <h1 class="text-secondary text-2xl mr-4">Sources</h1>
                    <button
                        @click="() => (currentTab = 'leads')"
                        class="text-primary-content md:text-lg px-2 py-1 border-secondary border rounded-md"
                        :class="{
                            'bg-secondary': currentTab === 'leads',
                        }"
                    >
                        Leads
                    </button>
                    <button
                        @click="() => (currentTab = 'members')"
                        class="text-primary-content md:text-lg px-2 py-1 border-secondary border rounded-md ml-2"
                        :class="{
                            'bg-secondary': currentTab === 'members',
                        }"
                    >
                        Members
                    </button>
                </div>

                <ul class="px-4 mt-4">
                    <li
                        v-if="currentTab === 'leads'"
                        v-for="(srcObj, ix) in leadTypes"
                        class="flex items-center"
                    >
                        <input
                            type="checkbox"
                            :id="srcObj.name"
                            :checked="leadTypes[ix].selected"
                            v-model="leadTypes[ix].selected"
                        />
                        <label
                            :for="srcObj.name"
                            class="ml-4 text-lg my-1 capitalize"
                            >{{ srcObj.name }}</label
                        >
                    </li>
                    <li
                        v-if="currentTab === 'members'"
                        v-for="(srcObj, ix) in membershipTypes"
                        class="flex items-center"
                    >
                        <input
                            type="checkbox"
                            :id="srcObj?.id"
                            :checked="membershipTypes[ix].selected"
                            v-model="membershipTypes[ix].selected"
                        />
                        <label
                            :for="srcObj?.id"
                            class="ml-4 text-lg my-1 capitalize"
                            >{{ srcObj.name }}</label
                        >
                    </li>
                </ul>
                <div class="flex flex-col py-2 mt-2">
                    <label
                        class="text-2xl mb-4 text-secondary"
                        for="audience-name"
                    >
                        Audience Title
                    </label>
                    <input
                        v-model="titleField"
                        id="audience-name"
                        type="text"
                    />
                </div>
            </div>
            <div class="flex justify-between max-w-sm w-full mt-6">
                <button
                    @click="
                        () => {
                            clearSelected();
                            $emit('cancel');
                        }
                    "
                    :disabled="isProcessing"
                >
                    Back
                </button>
                <button
                    @click="handleSave"
                    :disabled="isProcessing"
                    class="px-2 py-1 border-secondry border rounded-md hover:bg-secondary transition-all disabled:opacity-20 disabled:cursor-not-allowed"
                >
                    Save
                </button>
                <span class="w-10"></span>
            </div>
        </template>
    </daisy-modal>
</template>

<script setup>
import * as _ from "lodash";
import { ref, computed, onMounted, watch } from "vue";
import mutations from "@/gql/mutations";
import { useMutation, useQuery } from "@vue/apollo-composable";
import { toastInfo, toastError } from "@/utils/createToast";
import queries from "@/gql/queries";

import Spinner from "@/Components/Spinner.vue";
import DaisyModal from "@/Components/DaisyModal.vue";

const emit = defineEmits(["cancel", "update"]);

const props = defineProps({
    audience_id: {
        type: String,
        default: "",
    },
    membershipTypes: {
        type: Array,
        required: true,
    },
    leadTypes: {
        type: Array,
        required: true,
    },
});

const isProcessing = ref(true);

const { result, loading, error, refetch } = useQuery(queries["audience"].edit, {
    id: props.audience_id,
});

watch(loading, (nv, ov) => {
    if (!!resource?.value?.filters?.membership_type_id?.length > 0) {
        currentTab.value = "members";
    }

    setupTypes();
    isProcessing.value = false;
});

const currentTab = ref("leads");
const titleField = ref("");

const resource = computed(() => {
    if (result.value && result.value["audience"]) {
        return _.cloneDeep(result.value["audience"]);
    } else return null;
});

const { mutate: createAudience } = useMutation(mutations.audience.create);
const { mutate: updateAudience } = useMutation(mutations.audience.update);

const operation = computed(() => {
    return props.audience_id === "" ? "createAudience" : "updateAudience";
});

const operFn = computed(() => {
    return props.audience_id === "" ? createAudience : updateAudience;
});

const handleSave = async () => {
    isProcessing.value = true;

    let input = {
        id: resource?.value?.id,
        name: titleField.value,
        filters: {
            lead_type_id: currentTab.value === "leads" ? idsLead.value : [],
            membership_type_id:
                currentTab.value === "leads" ? [] : idsMember.value,
        },
        entity: "App\\Domain\\EndUsers\\Leads\\Projections\\Lead",
    };

    if (props.audience_id === "") {
        delete input["id"];
    }

    try {
        const { data } = await operFn.value({ input });
        let result = { ...data[operation.value] };
        toastInfo("Audience Saved");
        emit("update", result);
        clearSelected();
        isProcessing.value = false;
    } catch (error) {
        toastError("Error saving audience");
        console.log("error:", error);
        isProcessing.value = false;
    }
};

const leadTypes = ref(props.leadTypes);
const membershipTypes = ref(props.membershipTypes);
const allSources = computed(() => {
    return [...leadTypes.value, ...membershipTypes.value];
});
const selectedSources = computed(() =>
    allSources.value.filter((s) => s.selected)
);
const selectedIds = computed(() => selectedSources.value.map((s) => s.id));

const clearSelected = () => {
    return allSources.value.forEach((src) => {
        src.selected = false;
        return src;
    });
};

const idsLead = computed(() => {
    let ids = [];
    leadTypes.value.forEach((obj) => {
        if (obj.selected) {
            ids.push(obj.id);
        }
    });
    return ids;
});

const idsMember = computed(() => {
    let ids = [];
    membershipTypes.value.forEach((obj) => {
        if (obj.selected) {
            ids.push(obj.id);
        }
    });
    return ids;
});

const setupTypes = () => {
    let filters = resource?.value?.filters;
    if (!filters) return;

    let entrySources = filters?.lead_type_id;
    let memberSources = filters?.membership_type_id;

    if (filters?.membership_type_id instanceof Array) {
        membershipTypes.value = membershipTypes.value.map((s) => {
            s.id = `${s.id}`;
            return filters?.membership_type_id?.includes(s.id)
                ? { ...s, selected: true }
                : s;
        });
    }

    if (filters?.lead_type_id instanceof Array) {
        leadTypes.value = leadTypes.value.map((s) => {
            s.id = `${s.id}`;
            return filters?.lead_type_id?.includes(s.id)
                ? { ...s, selected: true }
                : s;
        });
    }

    titleField.value = resource?.value?.name;
};

onMounted(() => {
    refetch();
});
</script>
