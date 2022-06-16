<template>
    <div class="col-span-6 flex flex-col items-center gap-4 mb-4">
        <div
            class="w-32 h-32 rounded-full overflow-hidden border"
            :style="borderStyle"
        >
            <img
                v-if="fileForm.url"
                :src="fileForm.url"
                alt="lead profile picture"
                class="w-full h-full object-cover"
            />
            <img
                v-else-if="lead?.profile_picture?.misc?.url"
                :src="lead.profile_picture.misc.url"
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
        <!--        <label>-->
        <!--            <p class="text-white">-->
        <!--                {{ form["first_name"] }} {{ form["middle_name"] }}-->
        <!--                {{ form["last_name"] }}-->
        <!--            </p>-->
        <!--        </label>-->

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
            <div class="form-control col-span-2">
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
            <div class="form-control col-span-2">
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
            <div class="form-control col-span-2">
                <jet-label for="last_name" value="Last Name" />
                <input id="last_name" type="text" v-model="form['last_name']" />
                <jet-input-error
                    :message="form.errors['last_name']"
                    class="mt-2"
                />
            </div>
            <div class="form-control col-span-2">
                <jet-label for="email" value="Email" />
                <input id="email" type="email" v-model="form.email" />
                <jet-input-error :message="form.errors.email" class="mt-2" />
            </div>
            <div class="form-control col-span-2">
                <jet-label for="primary_phone" value="Primary Phone" />
                <input
                    id="primary_phone"
                    type="tel"
                    v-model="form['primary_phone']"
                />
                <jet-input-error
                    :message="form.errors.primary_phone"
                    class="mt-2"
                />
            </div>
            <div class="form-control col-span-2">
                <jet-label for="alternate_phone" value="Alternate Phone" />
                <input
                    id="alternate_phone"
                    type="tel"
                    v-model="form['alternate_phone']"
                />
                <jet-input-error
                    :message="form.errors.alternate_phone"
                    class="mt-2"
                />
            </div>

            <div class="form-control col-span-2">
                <jet-label for="gender" value="Gender" />
                <select class="" v-model="form['gender']" required id="gender">
                    <option value="">Select a Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
                <jet-input-error :message="form.errors.gender" class="mt-2" />
            </div>
            <div class="form-control col-span-2">
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
                class="form-control col-span-2"
                v-if="lead['agreement_number']"
            >
                <jet-label for="agreement_number" value="Agreement Number" />
                <input
                    disabled
                    type="text"
                    v-model="lead['agreement_number']"
                    autofocus
                    class="opacity-70"
                    id="agreement_number"
                />
            </div>

            <div class="form-control col-span-2" v-if="lead?.misc">
                <jet-label for="json_viewer" value="Additional Data" />
                <vue-json-pretty
                    :data="lead.misc"
                    id="json_viewer"
                    class="bg-base-200 border border-2 border-base-content border-opacity-10 rounded-lg p-2"
                />
            </div>
            <div class="form-control col-span-2" v-if="lead['external_id']">
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
            <div class="form-control col-span-2">
                <jet-label for="club_id" value="Club" />
                <select
                    class=""
                    v-model="form['gr_location_id']"
                    required
                    id="club_id"
                >
                    <option value="">Select a Club</option>
                    <option v-for="(name, clubId) in locations" :value="clubId">
                        {{ name }}
                    </option>
                </select>
                <jet-input-error
                    :message="form.errors['gr_location_id']"
                    class="mt-2"
                />
            </div>
            <div class="form-control col-span-2">
                <jet-label for="lead_source_id" value="Source" />
                <select
                    class=""
                    v-model="form['lead_source_id']"
                    required
                    id="lead_source_id"
                >
                    <option value="">Select a Source</option>
                    <option
                        v-for="(source, i) in lead_sources"
                        :value="source.id"
                    >
                        {{ source.name }}
                    </option>
                </select>
                <jet-input-error
                    :message="form.errors['lead_source_id']"
                    class="mt-2"
                />
            </div>
            <div class="form-control col-span-2">
                <jet-label for="lead_type_id" value="Lead Type" />
                <select
                    class=""
                    v-model="form['lead_type_id']"
                    required
                    id="lead_type_id"
                >
                    <option value="">Select a Lead Type</option>
                    <option v-for="(lead, i) in lead_types" :value="lead.id">
                        {{ lead.name }}
                    </option>
                </select>
                <jet-input-error
                    :message="form.errors['lead_type_id']"
                    class="mt-2"
                />
            </div>

            <div class="form-control col-span-2">
                <jet-label for="lead_owner" value="Lead Owner" />
                <select
                    class=""
                    v-model="form['lead_owner']"
                    required
                    id="lead_owner"
                >
                    <option value="">Select a Lead Owner</option>
                    <option v-for="(oname, uid) in lead_owners" :value="uid">
                        {{ oname }}
                    </option>
                </select>
                <jet-input-error
                    :message="form.errors['lead_owner']"
                    class="mt-2"
                />
            </div>

            <div class="form-control col-span-2">
                <jet-label for="lead_owner" value="Lead Status" />
                <select class="" v-model="form['lead_status']" id="lead_status">
                    <option value="">Select a Lead Status</option>
                    <option
                        v-for="(status, idx) in lead_statuses"
                        :value="status.id"
                    >
                        {{ status.status }}
                    </option>
                </select>
                <jet-input-error
                    :message="form.errors['lead_status']"
                    class="mt-2"
                />
            </div>
            <div class="form-control col-span-2">
                <jet-label for="opportunity" value="Select Opportunity" />
                <select
                    class=""
                    v-model="form['opportunity']"
                    required
                    id="opportunity"
                >
                    <option value="">Select Opportunity</option>
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
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
                    :message="form.errors['notes']?.title"
                    class="mt-2"
                />
            </div>
            <div class="form-control col-span-6">
                <jet-label for="notes" value="Body" />
                <textarea v-model="form['notes'].note" id="notes" />
                <jet-input-error
                    :message="form.errors['notes']?.note"
                    class="mt-2"
                />
            </div>
            <template v-if="lead?.all_notes?.length">
                <div class="text-sm font-medium col-span-6">Existing Notes</div>
                <div
                    class="collapse col-span-6"
                    tabindex="0"
                    v-for="note in lead.all_notes"
                >
                    <div
                        class="collapse-title text-sm font-medium"
                        v-on:click="notesExpanded(note)"
                    >
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
                class="col-span-2"
            >
                Times Emailed:
                <span class="badge badge-success badge-outline">
                    {{ interactionCount.emailedCount }}
                </span>
            </div>
            <div
                v-if="typeof interactionCount !== 'undefined'"
                class="col-span-2"
            >
                Times Called:
                <span class="badge badge-error badge-outline">
                    {{ interactionCount.calledCount }}
                </span>
            </div>
            <div
                v-if="typeof interactionCount !== 'undefined'"
                class="col-span-2"
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
                :disabled="form.processing"
                :loading="form.processing"
            >
                {{ buttonText }}
            </Button>
        </template>
    </jet-form-section>
</template>

<script>
import { computed, watchEffect } from "vue";
import { useForm } from "@inertiajs/inertia-vue3";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faUserCircle } from "@fortawesome/pro-solid-svg-icons";
import Vapor from "laravel-vapor";
import AppLayout from "@/Layouts/AppLayout";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";
import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";
import { useGoBack } from "@/utils";
import DatePicker from "@vuepic/vue-datepicker";
import VueJsonPretty from "vue-json-pretty";
import "@vuepic/vue-datepicker/dist/main.css";

library.add(faUserCircle);

export default {
    components: {
        AppLayout,
        Button,
        JetFormSection,
        FontAwesomeIcon,
        JetInputError,
        JetLabel,
        DatePicker,
        VueJsonPretty,
    },
    props: [
        "userId",
        "clientId",
        "lead",
        "locations",
        "lead_types",
        "lead_sources",
        "lead_owners",
        "lead_statuses",
        "interactionCount",
    ],
    computed: {
        borderStyle() {
            let color = "transparent";
            switch (this.form["opportunity"]) {
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
    },
    setup(props, context) {
        function notesExpanded(note) {
            axios.post(route("note.seen"), {
                client_id: props.clientId,
                note: note,
            });
        }

        let lead = props.lead;
        let operation = "Update";
        let leadData = null;
        if (!lead) {
            leadData = {
                first_name: null,
                middle_name: null,
                last_name: null,
                email: null,
                primary_phone: null,
                alternate_phone: null,
                club_id: null,
                client_id: props.clientId,
                gr_location_id: null,
                lead_type_id: null,
                lead_source_id: null,
                profile_picture: null,
                gender: "",
                date_of_birth: "",
                opportunity: "",
                lead_owner: props.userId,
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
                primary_phone: lead.primary_phone,
                alternate_phone: lead.alternate_phone,
                club_id: lead.club_id,
                client_id: props.clientId,
                gr_location_id: lead.gr_location_id,
                lead_type_id: lead.lead_type_id,
                lead_source_id: lead.lead_source_id,
                profile_picture: null,
                gender: lead.gender,
                agreement_number: lead.agreement_number,
                date_of_birth: lead.date_of_birth,
                opportunity: lead.opportunity,
                notes: { title: "", note: "" },
            };

            leadData["lead_owner"] =
                "lead_owner" in lead && lead.lead_owner !== null
                    ? lead.lead_owner.value
                    : "";
            leadData["lead_status"] =
                "lead_status" in lead && lead.lead_status !== null
                    ? lead.lead_status.value
                    : "";
        }
        const lastUpdated = computed(() =>
            "last_updated" in lead && lead.last_updated
                ? `Last Updated by ${lead.last_updated.value} at ${new Date(
                      lead.last_updated.updated_at
                  ).toLocaleDateString("en-US")}`
                : "This lead has never been updated"
        );
        const form = useForm(leadData);
        const fileForm = useForm({ file: null });

        const transformFormSubmission = (data) => {
            if (!data.notes?.title) {
                delete data.notes;
            }
            return data;
        };

        let handleSubmit = () =>
            form
                .transform(transformFormSubmission)
                .put(`/data/leads/${lead.id}`, {
                    preserveState: false,
                });

        if (operation === "Create") {
            handleSubmit = () =>
                form
                    .transform(transformFormSubmission)
                    .post("/data/leads/create", {
                        onSuccess: () => (form.notes = { title: "", note: "" }),
                    });
        }

        const goBack = useGoBack(route("data.leads"));

        watchEffect(async () => {
            console.log("file Changed!", fileForm.file);
            if (!fileForm.file) {
                return;
            }
            try {
                // uploadProgress.value=0;
                let response = await Vapor.store(fileForm.file, {
                    // visibility: form.isPublic ? 'public-read' : null,
                    visibility: "public-read",
                    // progress: (progress) => {
                    //     uploadProgress.value = Math.round(progress * 100);
                    // },
                });
                fileForm.url = `https://${response.bucket}.s3.amazonaws.com/${response.key}`;

                form.profile_picture = {
                    uuid: response.uuid,
                    key: response.key,
                    extension: response.extension,
                    bucket: response.bucket,
                };
            } catch (e) {
                console.error(e);
                // uploadProgress.value = -1;
            }
        });
        return {
            form,
            fileForm,
            buttonText: operation,
            handleSubmit,
            goBack,
            lastUpdated,
            operation,
            notesExpanded,
        };
    },
};
</script>

<style scoped>
input[type="text"],
input[type="email"],
input[type="tel"] {
    @apply w-full mt-1;
}

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
