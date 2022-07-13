<template>
    <div class="flex flex-col items-center mb-4">
        <div class="w-full mb-8 mt-4">
            <div class="grid grid-cols-12 w-full gap-4">
                <div
                    class="col-span-12 lg:col-span-4 flex-shrink-0 bg-base-300 rounded-lg flex flex-col p-4"
                >
                    <inertia-link
                        :href="route('data.leads.edit', leadId)"
                        class="flex flex-col items-center justify-center"
                    >
                        <div class="rounded-full border" :style="borderStyle">
                            <font-awesome-icon
                                icon="user-circle"
                                size="6x"
                                class="self-center opacity-10"
                            />
                        </div>
                        <h1 class="text-center text-2xl">
                            {{ firstName }}
                            {{ middleName }}
                            {{ lastName }}
                        </h1>
                        <div class="badge badge-success mt-4">
                            Agreement #: {{ agreementNumber }}
                        </div>
                        <div
                            class="badge badge-info mt-4"
                            v-if="trialDates?.length"
                        >
                            Trial Uses: {{ trialDates?.length || 0 }}
                        </div>
                        <div
                            class="badge badge-error mt-4"
                            v-if="trialMemberships?.length"
                        >
                            Trial Expires:
                            {{
                                new Date(
                                    trialMemberships[0].expiry_date
                                ).toLocaleString()
                            }}
                        </div>
                    </inertia-link>

                    <!--                        <ul class="w-full">-->
                    <!--                            <li class="mb-4"><p><b>Email -</b> {{ email }}</p></li>-->
                    <!--                            <li><p><b>Phone -</b> {{ phone }}</p></li>-->
                    <!--                        </ul>-->
                    <div
                        class="flex flex-row mt-8 self-center"
                        v-if="claimedByUser"
                    >
                        <div class="mr-4">
                            <Button
                                type="button"
                                success
                                @click="activeContactMethod = 'email'"
                                >Email
                                <span
                                    class="bg-base-300 p-1 rounded text-success text-xs ml-2"
                                >
                                    {{ interactionCount.emailedCount }}
                                </span>
                            </Button>
                        </div>
                        <div class="mr-4">
                            <Button
                                type="button"
                                error
                                @click="activeContactMethod = 'phone'"
                                >Call
                                <span
                                    class="bg-base-300 p-1 rounded text-error text-xs ml-2"
                                >
                                    {{ interactionCount.calledCount }}
                                </span>
                            </Button>
                        </div>
                        <div class="mr-4">
                            <Button
                                type="button"
                                info
                                @click="activeContactMethod = 'sms'"
                                >SMS
                                <span
                                    class="bg-base-300 p-1 rounded text-info text-xs ml-2"
                                >
                                    {{ interactionCount.smsCount }}
                                </span>
                            </Button>
                        </div>
                    </div>
                </div>
                <div
                    class="col-span-12 lg:col-span-8 rounded-lg bg-base-300 p-4"
                >
                    <CommsHistory :details="details" ref="commsHistoryRef" />
                </div>
                <daisy-modal
                    id="showViewModal"
                    ref="showViewModal"
                    @close="activeContactMethod = ''"
                >
                    <div
                        class="col-span-12 lg:col-span-8 rounded-lg bg-base-300 p-4"
                    >
                        <CommsActions
                            :activeContactMethod="activeContactMethod"
                            :phone="phone"
                            :lead-id="leadId"
                            @done="$refs.showViewModal.close()"
                        />
                    </div>
                </daisy-modal>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import { computed } from "@vue/reactivity";
import Button from "@/Components/Button.vue";
import FormSection from "@/Jetstream/FormSection.vue";
import CommsHistory from "./CommsHistory.vue";
import CommsActions from "./CommsActions.vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faUserCircle } from "@fortawesome/pro-solid-svg-icons";
import { library } from "@fortawesome/fontawesome-svg-core";
import DaisyModal from "@/Components/DaisyModal.vue";

library.add(faUserCircle);

const props = defineProps({
    userId: {
        type: String,
    },
    leadId: {
        type: String,
    },
    firstName: {
        type: String,
    },
    middleName: {
        type: String,
    },
    lastName: {
        type: String,
    },
    email: {
        type: String,
    },
    phone: {
        type: String,
    },
    opportunity: {
        type: String,
    },
    details: {
        type: Array,
    },
    trialDates: {
        type: String,
    },
    trialMemberships: {
        type: Array,
    },
    trialMembershipTypes: {
        type: Array,
    },
    interactionCount: {
        type: Number,
    },
    agreementNumber: {
        type: Number,
    },
    ownerUserId: {
        type: Number,
    },
});
const activeContactMethod = ref("");
const borderStyle = computed({
    get() {
        let color = "transparent";

        switch (props.opportunity) {
            case "High":
                color = "green";
                break;

            case "Medium":
                color = "yellow";
                break;

            case "Low":
                color = "red";
                break;
        }

        return {
            "border-color": color,
            "border-width": "5px",
        };
    },
});
const claimedByUser = computed({
    get() {
        let r = false;
        if ((props.owner_user_id = props.userId)) {
            r = true;
        }
        return r;
    },
});
const modalTitle = computed({
    get() {
        switch (activeContactMethod.value) {
            case "email":
                return "Email Lead";
            case "phone":
                return "Call Lead";
            case "sms":
                return "Text Lead";
        }
    },
});
const commsHistoryRef = ref(null);
function goToLeadDetailIndex(index) {
    console.log("goToLeadDetailIndex", { index, commsHistoryRef });
    if (index !== undefined && index !== null) {
        commsHistoryRef.value.goToLeadDetailIndex(index);
    }
}
const showViewModal = ref(null);
watch([activeContactMethod], () => {
    activeContactMethod.value && showViewModal.value.open();
});
defineExpose({
    goToLeadDetailIndex,
});
</script>
