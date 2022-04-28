<template>
    <jet-form-section @submitted="handleSubmit">
        <template #form>
            <div class="col-span-6 flex flex-col items-start gap-8">
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
                <label
                ><p class="text-white">
                    {{ form["first_name"] }} {{ form["middle_name"] }}
                    {{ form["last_name"] }}
                </p></label
                >

                <label class="btn btn-primary">
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
            <div
                class="form-control col-span-3"
                v-if="form['agreement_number']"
            >
                <jet-label for="first_name" value="Agreement Number"/>
                <input
                    disabled
                    type="text"
                    v-model="form['agreement_number']"
                    autofocus
                    class="opacity-70"
                />
            </div>
            <div class="form-divider"/>
            <div class="form-control col-span-2">
                <jet-label for="first_name" value="First Name"/>
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
                <jet-label for="middle_name" value="Middle Name"/>
                <input
                    id="middle_name"
                    type="text"
                    v-model="form['middle_name']"
                    autofocus
                />
                <jet-input-error
                    :message="form.errors['middle_name']"
                    class="mt-2"
                />
            </div>
            <div class="form-control col-span-2">
                <jet-label for="last_name" value="Last Name"/>
                <input
                    id="last_name"
                    type="text"
                    v-model="form['last_name']"
                    autofocus
                />
                <jet-input-error
                    :message="form.errors['last_name']"
                    class="mt-2"
                />
            </div>
            <div class="form-control col-span-6">
                <jet-label for="email" value="Email"/>
                <input id="email" type="email" v-model="form.email" autofocus/>
                <jet-input-error :message="form.errors.email" class="mt-2"/>
            </div>
            <div class="form-control col-span-3">
                <jet-label for="primary_phone" value="Primary Phone"/>
                <input
                    id="primary_phone"
                    type="tel"
                    v-model="form['primary_phone']"
                    autofocus
                />
                <jet-input-error
                    :message="form.errors.primary_phone"
                    class="mt-2"
                />
            </div>
            <div class="form-control col-span-3">
                <jet-label for="alternate_phone" value="Alternate Phone"/>
                <input
                    id="alternate_phone"
                    type="tel"
                    v-model="form['alternate_phone']"
                    autofocus
                />
                <jet-input-error
                    :message="form.errors.alternate_phone"
                    class="mt-2"
                />
            </div>
            <div class="form-control col-span-3">
                <jet-label for="gender" value="Gender"/>
                <select class="" v-model="form['gender']" required id="gender">
                    <option value="">Select a Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
                <jet-input-error :message="form.errors.gender" class="mt-2"/>
            </div>
            <div class="form-control col-span-3">
                <jet-label for="date_of_birth" value="Date of Birth"/>
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

            <div class="form-divider"/>
            <div class="form-control col-span-3">
                <jet-label for="club_id" value="Club"/>
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

            <div class="form-divider"/>
            <jet-label for="notes" value="Notes"/>
            <div class="form-control col-span-6">
                <jet-label for="notes.title" value="Title"/>
                <input type="text" v-model="form['notes'].title" id="notes.title"/>
                <jet-input-error :message="form.errors['notes']?.title" class="mt-2"/>
            </div>
            <div class="form-control col-span-6">
                <jet-label for="notes" value="Body"/>
                <textarea v-model="form['notes'].note" id="notes"/>
                <jet-input-error :message="form.errors['notes']?.note" class="mt-2"/>
            </div>
            <template v-if="lead?.all_notes?.length"
            >
                <div class="text-sm font-medium col-span-6">
                    Existing Notes
                </div>
                <div
                    class="collapse col-span-6"
                    tabindex="0"
                    v-for="note in lead.all_notes"
                >
                    <div class="collapse-title text-sm font-medium">
                        > {{ note.title }}
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
            <!--                class="col-span-3"-->
            <!--            >-->
            <!--                Times Emailed:-->
            <!--                <span class="badge badge-success badge-outline">-->
            <!--                    {{ interactionCount.emailedCount }}-->
            <!--                </span>-->
            <!--            </div>-->
            <!--            <div-->
            <!--                v-if="typeof interactionCount !== 'undefined'"-->
            <!--                class="col-span-3"-->
            <!--            >-->
            <!--                Times Called:-->
            <!--                <span class="badge badge-error badge-outline">-->
            <!--                    {{ interactionCount.calledCount }}-->
            <!--                </span>-->
            <!--            </div>-->
            <!--            <div-->
            <!--                v-if="typeof interactionCount !== 'undefined'"-->
            <!--                class="col-span-3"-->
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
            <div class="flex-grow"/>
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
import {computed, watchEffect} from "vue";
import {useForm} from "@inertiajs/inertia-vue3";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {library} from "@fortawesome/fontawesome-svg-core";
import {faUserCircle} from "@fortawesome/pro-solid-svg-icons";
import Vapor from "laravel-vapor";
import AppLayout from "@/Layouts/AppLayout";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";
import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";
import {useGoBack} from "@/utils";
import DatePicker from "vue3-date-time-picker";
import "vue3-date-time-picker/dist/main.css";

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
    },
    props: ["userId", "clientId", "member", "locations", "interactionCount"],
    setup(props, context) {
        const transformDate = (date) => {
            if (!date?.toISOString) {
                return date;
            }

            return date.toISOString().slice(0, 19).replace("T", " ");
        };

        let member = props.member;
        let operation = "Update";
        let memberData = null;
        if (!member) {
            memberData = {
                first_name: null,
                middle_name: null,
                last_name: null,
                email: null,
                primary_phone: null,
                alternate_phone: null,
                club_id: null,
                client_id: props.clientId,
                gr_location_id: null,
                profile_picture: null,
                gender: "",
                date_of_birth: "",
                notes: {title: "", note: ""},
            };
            operation = "Create";
        } else {
            memberData = {
                first_name: member.first_name,
                middle_name: member.middle_name,
                last_name: member.last_name,
                email: member.email,
                primary_phone: member.primary_phone,
                alternate_phone: member.alternate_phone,
                club_id: member.club_id,
                client_id: props.clientId,
                gr_location_id: member.gr_location_id,
                profile_picture: null,
                gender: member.gender,
                notes: {title: "", note: ""},
                date_of_birth: member.date_of_birth,
            };
        }
        const lastUpdated = computed(() =>
            "last_updated" in member && member.last_updated
                ? `Last Updated by ${member.last_updated.value} at ${new Date(
                    member.last_updated.updated_at
                ).toLocaleDateString("en-US")}`
                : "This member has never been updated"
        );
        const form = useForm(memberData);
        const fileForm = useForm({file: null});

        let handleSubmit = () =>
            form
                .transform((data) => ({
                    ...data,
                    date_of_birth: transformDate(data.date_of_birth),
                }))
                .put(route("data.members.update", member.id), {
                    preserveState: false,
                });
        if (operation === "Create") {
            handleSubmit = () =>
                form
                    .transform((data) => ({
                        ...data,
                        date_of_birth: transformDate(data.date_of_birth),
                    }))
                    .post(route("data.members.store"), {
                        onSuccess: () => (form.notes = {title: "", note: ""}),
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
    @apply col-span-6 border-t-2 border-base-content border-opacity-10 relative;
}

.form-divider > span {
    @apply absolute inset-0  transform -translate-y-1/2 text-xs text-opacity-30 bg-base-300 block;
}
</style>
