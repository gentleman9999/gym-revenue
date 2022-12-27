<template>
    <div class="col-span-6 flex flex-col items-center gap-4 mb-4">
        <div
            class="w-32 h-32 rounded-full overflow-hidden border"
            :style="borderStyle"
        >
            <img
                v-if="fileForm.url"
                :src="fileForm.url"
                alt="member profile picture"
                class="w-full h-full object-cover"
            />
            <img
                v-else-if="member?.profile_picture?.url"
                :src="member.profile_picture.url"
                alt="member profile picture"
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
            <div class="form-control col-span-6 md:col-span-2">
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
                <jet-label for="primary_phone" value="Primary Phone" />
                <phone-input id="primary_phone" v-model="form['phone']" />
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
                <select class="" v-model="form['gender']" required id="gender">
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
                v-if="member?.agreement_number"
            >
                <jet-label for="agreement_number" value="Agreement Number" />
                <input
                    disabled
                    type="text"
                    v-model="member.agreement_number"
                    class="opacity-70"
                    id="agreement_number"
                />
            </div>

            <div
                class="form-control md:col-span-2 col-span-6"
                v-if="member?.external_id"
            >
                <jet-label for="external_id" value="External ID" />
                <input
                    disabled
                    type="text"
                    v-model="member.external_id"
                    class="opacity-70"
                    id="external_id"
                />
            </div>
            <div
                class="form-control md:col-span-2 col-span-6"
                v-if="member?.misc"
            >
                <jet-label for="json_viewer" value="Additional Data" />
                <vue-json-pretty
                    :data="member.misc"
                    id="json_viewer"
                    class="bg-base-200 border border-2 border-base-content border-opacity-10 rounded-lg p-2"
                />
            </div>

            <div class="form-divider" />
            <div class="form-control md:col-span-2 col-span-6">
                <jet-label for="club_id" value="Club" />
                <select
                    class=""
                    v-model="form['gr_location_id']"
                    required
                    id="club_id"
                >
                    <option value="">Select a Club</option>
                    <option
                        v-for="(name, clubId) in locations"
                        :value="clubId"
                        :key="clubId"
                    >
                        {{ name }}
                    </option>
                </select>
                <jet-input-error
                    :message="form.errors['gr_location_id']"
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
            <template v-if="member?.all_notes?.length">
                <div class="text-sm font-medium col-span-6">Existing Notes</div>
                <div
                    class="collapse col-span-6"
                    tabindex="0"
                    v-for="note in member.all_notes"
                    :key="note.id"
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

            <!--            <div-->
            <!--                v-if="typeof interactionCount !== 'undefined'"-->
            <!--                class="form-divider"-->
            <!--            />-->
            <!--            <div-->
            <!--                v-if="typeof interactionCount !== 'undefined'"-->
            <!--                class="col-span-2"-->
            <!--            >-->
            <!--                Times Emailed:-->
            <!--                <span class="badge badge-success badge-outline">-->
            <!--                    {{ interactionCount.emailedCount }}-->
            <!--                </span>-->
            <!--            </div>-->
            <!--            <div-->
            <!--                v-if="typeof interactionCount !== 'undefined'"-->
            <!--                class="col-span-2"-->
            <!--            >-->
            <!--                Times Called:-->
            <!--                <span class="badge badge-error badge-outline">-->
            <!--                    {{ interactionCount.calledCount }}-->
            <!--                </span>-->
            <!--            </div>-->
            <!--            <div-->
            <!--                v-if="typeof interactionCount !== 'undefined'"-->
            <!--                class="col-span-2"-->
            <!--            >-->
            <!--                Times Text Messaged:-->
            <!--                <span class="badge badge-info badge-outline">-->
            <!--                    {{ interactionCount.smsCount }}-->
            <!--                </span>-->
            <!--            </div>-->

            <!--            <div class="form-divider" />-->
            <!--            <div class="col-span-6">-->
            <!--                <label v-if="operation === 'Update'">-->
            <!--                    {{ lastUpdated }}-->
            <!--                </label>-->
            <!--            </div>-->
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
                {{ buttonText }}
            </Button>
        </template>
    </jet-form-section>
</template>

<script>
import { computed, watchEffect } from "vue";
import { useGymRevForm } from "@/utils";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faUserCircle } from "@fortawesome/pro-solid-svg-icons";
import Vapor from "laravel-vapor";
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import { useGoBack } from "@/utils";
import DatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import { transformDate } from "@/utils/transformDate";
import PhoneInput from "@/Components/PhoneInput.vue";
import { usePage } from "@inertiajs/inertia-vue3";

library.add(faUserCircle);

export default {
    components: {
        Button,
        JetFormSection,
        FontAwesomeIcon,
        JetInputError,
        JetLabel,
        DatePicker,
        PhoneInput,
    },
    props: ["userId", "clientId", "member", "locations", "interactionCount"],
    setup(props, context) {
        const page = usePage();
        function notesExpanded(note) {
            axios.post(route("note.seen"), {
                client_id: props.clientId,
                note: note,
            });
        }

        let member = props.member;
        let operation = "Update";
        let memberData = null;
        if (!member) {
            memberData = {
                first_name: "",
                middle_name: "",
                last_name: "",
                email: "",
                phone: "",
                alternate_phone: "",
                club_id: "",
                client_id: props.clientId,
                gr_location_id: null,
                profile_picture: null,
                gender: "",
                date_of_birth: null,
                notes: { title: "", note: "" },
            };
            operation = "Create";
        } else {
            memberData = {
                first_name: member.first_name,
                middle_name: member.middle_name,
                last_name: member.last_name,
                email: member.email,
                phone: member.phone,
                alternate_phone: member.alternate_phone,
                club_id: member.club_id,
                client_id: props.clientId,
                gr_location_id: member.gr_location_id,
                profile_picture: null,
                gender: member.gender,
                notes: { title: "", note: "" },
                date_of_birth: member.date_of_birth,
            };
        }
        const borderStyle = computed(() => {
            let color = "transparent";
            switch (form["opportunity"]) {
                case 3:
                    color = "green";
                    break;

                case 2:
                    color = "yellow";
                    break;

                case 1:
                    color = "red";
                    break;
            }
            return {
                "border-color": color,
                "border-width": "5px",
            };
        });
        const lastUpdated = computed(() =>
            "last_updated" in member && member.last_updated
                ? `Last Updated by ${member.last_updated.value} at ${new Date(
                      member.last_updated.updated_at
                  ).toLocaleDateString("en-US")}`
                : "This member has never been updated"
        );
        const form = useGymRevForm(memberData);
        const fileForm = useGymRevForm({ file: null });

        const transformFormSubmission = (data) => {
            if (!data.notes?.title) {
                delete data.notes;
            }
            data.date_of_birth = transformDate(data.date_of_birth);
            return data;
        };

        let handleSubmit = () =>
            form
                .dirty()
                .transform(transformFormSubmission)
                .put(route("data.members.update", member.id), {
                    preserveState: false,
                });
        if (operation === "Create") {
            handleSubmit = () =>
                form
                    .transform(transformFormSubmission)
                    .post(route("data.members.store"), {
                        onSuccess: () => (form.notes = { title: "", note: "" }),
                    });
        }

        const goBack = useGoBack(route("data.members"));

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
                let pfpResponse = await axios.post(
                    route("data.members.upload.profile.picture"),
                    [
                        {
                            id: response.uuid,
                            key: response.key,
                            filename: fileForm.file.name,
                            original_filename: fileForm.file.name,
                            extension: response.extension,
                            bucket: response.bucket,
                            size: fileForm.file.size,
                            entity_id: member.id,
                            /*client_id: page.props.value.user.client_id,*/
                            user_id: page.props.value.user.id,
                        },
                    ]
                );
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
            borderStyle,
        };
    },
};
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
