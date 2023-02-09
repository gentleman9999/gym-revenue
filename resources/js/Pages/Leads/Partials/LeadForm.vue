<template>
    <div class="col-span-6 flex flex-col items-center gap-4 mb-4">
        <div class="w-32 h-32 rounded-full overflow-hidden border">
            <img
                v-if="fileForm.url"
                :src="fileForm.url"
                alt="lead profile picture"
                class="w-full h-full object-cover"
            />
            <img
                v-else-if="lead?.profile_picture?.url"
                :src="lead?.profile_picture?.url"
                alt="lead profile picture"
                class="w-full h-full object-cover"
            />
            <font-awesome-icon
                v-else
                icon="user-circle"
                size="6x"
                class="opacity-10 !h-full !w-full"
            />
        </div>
        <label class="btn btn-secondary btn-sm btn-outline">
            <span>Upload Image</span>
            <input
                @input="fileForm.file = $event.target.files[0]"
                type="file"
                accept="image/*"
                hidden
                class="hidden"
            />
        </label>
    </div>
    <jet-form-section @submitted="handleSubmit">
        <template #form>
            <div class="form-control md:col-span-2 col-span-6">
                <jet-label for="first_name" value="First Name" />
                <input
                    id="first_name"
                    type="text"
                    v-model="form['first_name']"
                    autofocus
                />
                <jet-input-error
                    :message="form.errors['first_name']"
                    class="mt-2"
                />
            </div>
            <div class="form-control md:col-span-2 col-span-6">
                <jet-label for="middle_name" value="Middle Name" />
                <input
                    id="middle_name"
                    type="text"
                    v-model="form['middle_name']"
                />
                <jet-input-error
                    :message="form.errors['middle_name']"
                    class="mt-2"
                />
            </div>
            <div class="form-control md:col-span-2 col-span-6">
                <jet-label for="last_name" value="Last Name" />
                <input id="last_name" type="text" v-model="form['last_name']" />
                <jet-input-error
                    :message="form.errors['last_name']"
                    class="mt-2"
                />
            </div>
            <div class="form-control md:col-span-2 col-span-6">
                <jet-label for="email" value="Email" />
                <input id="email" type="email" v-model="form.email" />
                <jet-input-error :message="form.errors.email" class="mt-2" />
            </div>
            <div class="form-control md:col-span-2 col-span-6">
                <jet-label for="phone" value="Primary Phone" />
                <phone-input id="phone" v-model="form['phone']" />
                <jet-input-error :message="form.errors.phone" class="mt-2" />
            </div>
            <div class="form-control md:col-span-2 col-span-6">
                <jet-label for="alternate_phone" value="Alternate Phone" />
                <phone-input
                    id="alternate_phone"
                    v-model="form['alternate_phone']"
                />
                <jet-input-error
                    :message="form.errors.alternate_phone"
                    class="mt-2"
                />
            </div>

            <div class="form-control md:col-span-2 col-span-6">
                <jet-label for="gender" value="Gender" />
                <!--                TODO: create gender dropdown from dynamic data provided by gql  -->
                <select class="" v-model="form.gender" required id="gender">
                    <option value="">Select a Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
                <jet-input-error :message="form.errors.gender" class="mt-2" />
            </div>
            <div class="form-control md:col-span-2 col-span-6">
                <jet-label for="date_of_birth" value="Date of Birth" />
                <date-picker
                    v-model="form['date_of_birth']"
                    dark
                    :month-change-on-scroll="false"
                    :auto-apply="true"
                    :close-on-scroll="true"
                />
                <jet-input-error
                    :message="form.errors.date_of_birth"
                    class="mt-2"
                />
            </div>

            <div
                class="form-control md:col-span-2 col-span-6"
                v-if="lead?.misc"
            >
                <jet-label for="json_viewer" value="Additional Data" />
                <vue-json-pretty
                    :data="lead.misc"
                    id="json_viewer"
                    class="bg-base-200 border-2 border-base-content border-opacity-10 rounded-lg p-2"
                />
            </div>
            <div
                class="form-control md:col-span-2 col-span-6"
                v-if="lead?.external_id"
            >
                <jet-label for="external_id" value="External ID" />
                <input
                    disabled
                    type="text"
                    v-model="lead['external_id']"
                    class="opacity-70"
                    id="external_id"
                />
            </div>

            <div class="form-divider" />
            <div class="form-control md:col-span-2 col-span-6">
                <!-- <jet-label for="club_id" value="Club" />
                <select
                    class=""
                    v-model="form['home_location_id']"
                    required
                    id="club_id"
                >
                    <option value="">Select a Club</option>
                    <option
                        v-for="location in locations.data"
                        :key="location.id"
                        :value="location.id"
                    >
                        {{ location.name }}
                    </option>
                </select> -->
                <ClubSelect v-model="form.home_location_id" required />
                <jet-input-error
                    :message="form.errors.home_location_id"
                    class="mt-2"
                />
            </div>
            <!-- lead owner dropdown will not disable if field is only unchanged field -->
            <div class="form-control md:col-span-2 col-span-6">
                <jet-label for="lead_owner" value="Lead Owner" />
                <select
                    class=""
                    v-model="form['owner_user_id']"
                    required
                    id="lead_owner"
                >
                    <option value="">Select a Lead Owner</option>
                    <option
                        v-for="{ user } in lead_owners"
                        :value="user.id"
                        :key="user.id"
                    >
                        {{ user.name }}
                    </option>
                </select>
                <jet-input-error
                    :message="form.errors['owner_user_id']"
                    class="mt-2"
                />
            </div>
            <div class="form-control md:col-span-2 col-span-6">
                <jet-label for="opportunity" value="Select Opportunity" />
                <select
                    class=""
                    v-model="form['opportunity']"
                    required
                    id="opportunity"
                >
                    <option value="">Select Opportunity</option>
                    <option value="1">Low</option>
                    <option value="2">Medium</option>
                    <option value="3">High</option>
                </select>
                <jet-input-error
                    :message="form.errors.opportunity"
                    class="mt-2"
                />
            </div>
            <div class="form-divider" />
            <jet-label for="notes" value="Notes" />
            <div class="form-control col-span-6">
                <jet-label for="notes.title" value="Title" />
                <input
                    type="text"
                    v-model="form['notes'].title"
                    id="notes.title"
                />
                <jet-input-error
                    :message="form.errors['notes.title']"
                    class="mt-2"
                />
            </div>
            <div class="form-control col-span-6">
                <jet-label for="notes" value="Body" />
                <textarea v-model="form['notes'].note" id="notes" />
                <jet-input-error
                    :message="form.errors['notes.note']"
                    class="mt-2"
                />
            </div>
            <template v-if="lead?.all_notes?.length">
                <div class="text-sm font-medium col-span-6">Existing Notes</div>
                <div
                    class="collapse col-span-6"
                    tabindex="0"
                    v-for="(note, ndx) in lead.all_notes"
                    :key="note.id"
                >
                    <div
                        class="collapse-title text-sm font-medium"
                        v-on:click="notesExpanded(note)"
                    >
                        <hr
                            v-if="
                                ndx != 0 &&
                                lead.all_notes[ndx - 1]['lifecycle'] !=
                                    note['lifecycle']
                            "
                            class="pb-5"
                        />
                        > {{ note.title }}
                        <div
                            v-if="note.read == false"
                            class="badge badge-secondary"
                        >
                            unread
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 collapse-content">
                        <div
                            class="text-sm text-base-content text-opacity-80 bg-base-100 rounded-lg p-2"
                        >
                            {{ note.note }}
                        </div>
                    </div>
                </div>
            </template>

            <div
                v-if="typeof interactionCount !== 'undefined'"
                class="form-divider"
            />
            <div
                v-if="typeof interactionCount !== 'undefined'"
                class="md:col-span-2 col-span-6"
            >
                Times Emailed:
                <span class="badge badge-success badge-outline">
                    {{ interactionCount.emailedCount }}
                </span>
            </div>
            <div
                v-if="typeof interactionCount !== 'undefined'"
                class="md:col-span-2 col-span-6"
            >
                Times Called:
                <span class="badge badge-error badge-outline">
                    {{ interactionCount.calledCount }}
                </span>
            </div>
            <div
                v-if="typeof interactionCount !== 'undefined'"
                class="md:col-span-2 col-span-6"
            >
                Times Text Messaged:
                <span class="badge badge-info badge-outline">
                    {{ interactionCount.smsCount }}
                </span>
            </div>

            <div class="form-divider" />
            <div class="col-span-6">
                <label v-if="operation === 'Update'">
                    {{ lastUpdated }}
                </label>
            </div>
        </template>

        <template #actions>
            <Button
                type="button"
                @click="goBack"
                :class="{ 'opacity-25': form.processing }"
                error
                outline
                :disabled="form.processing"
            >
                Cancel
            </Button>
            <Button
                :class="{ 'opacity-25': form.processing }"
                class="btn-primary"
                :disabled="form.processing || !form.isDirty"
                :loading="form.processing"
            >
                {{ operation }}
            </Button>
        </template>
    </jet-form-section>
</template>

<script setup>
import { computed, watchEffect, onMounted } from "vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faUserCircle } from "@fortawesome/pro-solid-svg-icons";
import Vapor from "laravel-vapor";
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import { useGoBack, useGymRevForm } from "@/utils";
import DatePicker from "@vuepic/vue-datepicker";
import VueJsonPretty from "vue-json-pretty";
import "@vuepic/vue-datepicker/dist/main.css";
import { transformDate } from "@/utils/transformDate";
import PhoneInput from "@/Components/PhoneInput.vue";
import * as _ from "lodash";
import { usePage } from "@inertiajs/inertia-vue3";
import ClubSelect from "@/Pages/Locations/Partials/ClubSelect.vue";
import { useMutation } from "@vue/apollo-composable";
import mutations from "@/gql/mutations.js";

library.add(faUserCircle);

const emit = defineEmits(["done", "cancel"]);

const props = defineProps({
    userId: {
        type: [Object, String],
    },
    lead: {
        type: Object,
    },
    lead_owners: {
        type: Array,
    },
    lead_types: {
        type: Array,
    },
    lead_statuses: {
        type: Array,
    },
    entry_sources: {
        type: Array,
    },
    locations: {
        type: Array,
    },
});

const page = usePage();
function notesExpanded(note) {
    axios.post(route("note.seen"), {
        note: note,
    });
}

let lead = _.cloneDeep(props.lead);

onMounted(() => {
    console.log("LeadForm mounted, lead(props)", props.lead);
});

let operation = "Update";
let leadData = null;
if (!lead) {
    leadData = {
        first_name: "",
        middle_name: "",
        last_name: "",
        email: "",
        phone: "",
        alternate_phone: "",
        club_id: "",
        home_location_id: "",
        lead_type_id: "",
        entry_source_id: "",
        profile_picture: "",
        gender: "",
        date_of_birth: null,
        opportunity: "",
        owner_user_id: page.props.value.user.id,
        lead_status: "",
        notes: { title: "", note: "" },
    };
    operation = "Create";
} else {
    leadData = {
        first_name: lead.first_name,
        middle_name: lead.middle_name,
        last_name: lead.last_name,
        email: lead.email,
        phone: lead.phone,
        alternate_phone: lead.alternate_phone,
        club_id: lead.club_id,
        home_location_id: lead?.home_location?.id,
        lead_type_id: lead.lead_type_id,
        entry_source_id: lead.entry_source_id,
        profile_picture: lead.profile_picture,
        gender: lead.gender,
        agreement_number: lead.agreement_number,
        date_of_birth: lead.date_of_birth,
        opportunity: lead.opportunity,
        notes: { title: "", note: "" },
        owner_user_id: lead.owner_user_id,
        lead_status_id: props.lead?.lead_status?.id || "",
    };
}
const lastUpdated = computed(() =>
    "last_updated" in lead && lead.last_updated
        ? `Last Updated by ${lead.last_updated.value} at ${new Date(
              lead.last_updated.updated_at
          ).toLocaleDateString("en-US")}`
        : "This lead has never been updated"
);
const form = useGymRevForm(leadData);
const fileForm = useGymRevForm({ file: null });

const transformFormSubmission = (data) => {
    data.date_of_birth = transformDate(data.date_of_birth);
    return data;
};

const { mutate: createUser } = useMutation(mutations.user.create);
const { mutate: updateUser } = useMutation(mutations.user.update);

let handleSubmit = async () => {
    await updateUser({
        input: {
            id: props.lead.id,
            first_name: form.first_name,
            last_name: form.last_name,
            email: form.email,
            alternate_email: form.alternate_email,
            address1: form.address1,
            address2: form.address2,
            phone: form.phone,
            city: form.city,
            state: form.state,
            contact_preference: form.contact_preference,
            started_at: form.started_at,
            ended_at: form.ended_at,
            terminated_at: form.terminated_at,
            notes: form.notes,
            team_id: form.team_id,
            role_id: form.role_id,
            home_location_id: form.home_location_id,
            gender: form.gender,
            manager: null,
        },
    });

    emit("done");
};

if (operation === "Create") {
    handleSubmit = async () => {
        await createUser({
            input: {
                first_name: form.first_name,
                last_name: form.last_name,
                email: form.email,
                alternate_email: form.alternate_email,
                address1: form.address1,
                address2: form.address2,
                phone: form.phone,
                city: form.city,
                state: form.state,
                contact_preference: form.contact_preference,
                started_at: form.started_at,
                ended_at: form.ended_at,
                terminated_at: form.terminated_at,
                notes: form.notes,
                team_id: form.team_id,
                role_id: form.role_id,
                home_location_id: form.home_location_id,
                gender: form.gender,
                manager: null,
                departments: [],
                positions: [],
            },
        });
        emit("done");
    };
}

const goBack = useGoBack(route("data.leads"));

watchEffect(async () => {
    console.log("file Changed!", fileForm.file);
    if (!fileForm.file) {
        return;
    }
    try {
        let response = await Vapor.store(fileForm.file, {
            visibility: "public-read",
        });
        fileForm.url = `https://${response.bucket}.s3.amazonaws.com/${response.key}`;

        form.profile_picture = {
            uuid: response.uuid,
            key: response.key,
            extension: response.extension,
            bucket: response.bucket,
        };
        let pfpResponse = await axios.post(
            route("data.leads.upload.profile.picture"),
            [
                {
                    id: response.uuid,
                    key: response.key,
                    filename: fileForm.file.name,
                    original_filename: fileForm.file.name,
                    extension: response.extension,
                    bucket: response.bucket,
                    size: fileForm.file.size,
                    entity_id: lead.id,
                    user_id: page.props.value.user.id,
                    url: `https://${response.bucket}.s3.amazonaws.com/${response.key}`,
                },
            ]
        );
    } catch (e) {
        console.error(e);
    }
});
</script>

<style scoped>
input[type="text"],
input[type="email"],
select {
    @apply w-full;
}

.form-divider {
    @apply col-span-6 border-t-2 border-secondary relative;
}

.form-divider > span {
    @apply absolute inset-0  transform -translate-y-1/2 text-xs text-opacity-30 bg-base-300 block;
}
</style>
