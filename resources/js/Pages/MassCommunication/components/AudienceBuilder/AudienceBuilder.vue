<template>
    <!-- <ApolloQuery :query="(gql) => queries['audience'].edit" :variables="param"> -->

    <daisy-modal
        :open="true"
        :showCloseButton="false"
        :closable="false"
        class="h-full w-full bg-transparent border-none flex flex-col justify-center items-center shadow-none"
    >
        <template v-if="loading">
            <Spinner />
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
                    :disabled="loading"
                >
                    Back
                </button>
                <button
                    @click="handleSave"
                    :disabled="form.name === '' || loading"
                    class="px-2 py-1 border-secondry border rounded-md hover:bg-secondary transition-all disabled:opacity-20 disabled:cursor-not-allowed"
                >
                    Save
                </button>
                <span class="w-10"></span>
            </div>
        </template>
    </daisy-modal>
    <!-- </ApolloQuery> -->
</template>

<script setup>
import * as _ from "lodash";
import { ref, computed, onMounted, watch } from "vue";
import mutations from "@/gql/mutations";
import { useMutation, useQuery } from "@vue/apollo-composable";
import { useGymRevForm } from "@/utils";
import { toastInfo, toastError } from "@/utils/createToast";
import queries from "@/gql/queries";

import Spinner from "@/Components/Spinner.vue";
import DaisyModal from "@/Components/DaisyModal.vue";

const emit = defineEmits(["cancel", "update"]);

const props = defineProps({
    audience: {
        type: Object,
        default: {
            id: "",
            name: "",
            filters: {
                membership_type_id: [],
                lead_type_id: [],
            },
        },
    },
    audience_id: {
        type: String,
        default: "",
    },
    // id: {
    //     type: String,
    //     default: ""
    // },
    membershipTypes: {
        type: Array,
        required: true,
    },
    leadTypes: {
        type: Array,
        required: true,
    },
});

const {
    result,
    loading: modelLoading,
    error,
    refetch,
} = useQuery(queries["audience"].edit, {
    id: props.audience_id,
});

watch(modelLoading, (nv, ov) => {
    console.log("isLoadingNow updated from", ov, "to", nv);
    if (!modelLoading) {
        form.name = audienceData.name;
        form.id = audienceData.id;
        form.filters = audienceData.filters;
    }
    let leadLen = audienceData?.value?.filters?.lead_type_id;
    let memLen = audienceData?.value?.filters?.membership_type_id;

    console.log("lead", leadLen);
    console.log("mem", memLen);

    if (!leadLen && !!memLen) {
        currentTab.value = "members";
    }

    if (memLen?.length > leadLen?.length) {
        currentTab.value = "members";
    }

    setupTypes();
    titleField.value = audienceData?.value?.name;
    loading.value = false;
});

const loading = ref(true);
const currentTab = ref("leads");

const audienceData = computed(() => {
    return result.value?.audience;
});

const { mutate: createAudience } = useMutation(mutations.audience.create);
const { mutate: updateAudience } = useMutation(mutations.audience.update);

// const { result, loading, error, refetch } = useQuery()
const titleField = ref("");

const form = useGymRevForm({
    ...audienceData.value,
});

const operation = computed(() => {
    return props.audience.id === "" ? "createAudience" : "updateAudience";
});

const operFn = computed(() => {
    return props.audience.id === "" ? createAudience : updateAudience;
});

const handleSave = async () => {
    loading.value = true;

    let input = {
        id: audienceData.value?.id,
        name: titleField.value,
        filters: {
            lead_type_id: currentTab.value === "leads" ? idsLead.value : [],
            membership_type_id:
                currentTab.value === "leads" ? [] : idsMember.value,
        },
        entity: "App\\Domain\\EndUsers\\Leads\\Projections\\Lead",
    };

    if (props.audience.id === "") {
        delete input["id"];
    }

    try {
        const { data } = await operFn.value({ input });
        let result = { ...data[operation.value] };
        loading.value = false;
        toastInfo("Audience Saved");
        emit("update", result);
        clearSelected();
    } catch (error) {
        toastError("Error saving audience");
        console.log("error:", error);
        loading.value = false;
    }

    loading.value = false;
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
/**
 * check for invalid data and return an informative message
 * to the user to fix it before saving
 */
// const saveDisabled = computed(() => {
//     if (titleVal.value === "New Audience" || titleVal.value.trim() === "")
//         return "You must name your audience.";
//     if (selectedIds.value.length === 0)
//         return "You must select at least one filter.";
//     return false;
// });
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
    let filters = audienceData?.value?.filters;
    if (!filters) return;

    let leadSources = filters?.lead_type_id;
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
            return filters?.lead_type_id?.includes(s.id)
                ? { ...s, selected: true }
                : s;
        });
    }
};

onMounted(() => {
    refetch();
    // let filters = props.audience?.filters;
    // if (!filters) return;
    // membershipTypes.value = membershipTypes.value.map((s) => {
    //     return filters?.membership_type_id?.includes(s.id)
    //         ? { ...s, selected: true }
    //         : s;
    // });
    // leadTypes.value = leadTypes.value.map((s) => {
    //     return filters?.lead_type_id?.includes(s.id)
    //         ? { ...s, selected: true }
    //         : s;
    // });
});
</script>
