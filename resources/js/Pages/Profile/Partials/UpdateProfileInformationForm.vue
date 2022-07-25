<template>
    <jet-form-section @submitted="updateProfileInformation">
        <template #title> Profile Information </template>

        <template #description>
            Update your account's profile information and email address.
        </template>

        <template #form>
            <!-- Profile Photo -->
            <div
                class="col-span-6 sm:col-span-4"
                v-if="$page.props.jetstream.managesProfilePhotos"
            >
                <!-- Profile Photo File Input -->
                <input
                    type="file"
                    class="hidden"
                    ref="photo"
                    @change="updatePhotoPreview"
                />

                <jet-label for="photo" value="Photo" />

                <!-- Current Profile Photo -->
                <div class="mt-2" v-show="!photoPreview">
                    <img
                        :src="user.profile_photo_url"
                        :alt="user.name"
                        class="rounded-full h-20 w-20 object-cover"
                    />
                </div>

                <!-- New Profile Photo Preview -->
                <div class="mt-2" v-show="photoPreview">
                    <span
                        class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                        :style="
                            'background-image: url(\'' + photoPreview + '\');'
                        "
                    >
                    </span>
                </div>

                <jet-secondary-button
                    class="mt-2 mr-2"
                    type="button"
                    @click.prevent="selectNewPhoto"
                >
                    Select A New Photo
                </jet-secondary-button>

                <jet-secondary-button
                    type="button"
                    class="mt-2"
                    @click.prevent="deletePhoto"
                    v-if="user.profile_photo_path"
                >
                    Remove Photo
                </jet-secondary-button>

                <jet-input-error :message="form.errors.photo" class="mt-2" />
            </div>

            <!-- First Name -->
            <div class="col-span-2 sm:col-span-2">
                <jet-label for="first_name" value="First Name" />
                <input
                    id="first_name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form['first_name']"
                    autocomplete="name"
                />
                <jet-input-error
                    :message="form.errors['first_name']"
                    class="mt-2"
                />
            </div>

            <!-- Last Name -->
            <div class="col-span-2 sm:col-span-2">
                <jet-label for="last_name" value="Last Name" />
                <input
                    id="last_name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form['last_name']"
                    autocomplete="name"
                />
                <jet-input-error
                    :message="form.errors['last_name']"
                    class="mt-2"
                />
            </div>

            <!-- Phone # -->
            <div class="col-span-2 sm:col-span-2">
                <jet-label for="phone" value="Phone #" />
                <input
                    id="phone"
                    type="number"
                    class="mt-1 block w-full"
                    v-model="form.phone"
                />
                <jet-input-error :message="form.errors.phone" class="mt-2" />
            </div>

            <!-- Email -->
            <div class="col-span-2 sm:col-span-2">
                <jet-label for="email" value="Email" />
                <input
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                />
                <jet-input-error :message="form.errors.email" class="mt-2" />
            </div>

            <!-- alternate_email -->
            <div class="col-span-2 sm:col-span-2">
                <jet-label for="alternate_email" value="Personal Email" />
                <input
                    id="alternate_email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.alternate_email"
                />
                <jet-input-error
                    :message="form.errors.alternate_email"
                    class="mt-2"
                />
            </div>

            <!-- Address 1 -->
            <div class="col-span-6 sm:col-span-6">
                <jet-label for="address1" value="Address1" />
                <input
                    id="address1"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.address1"
                />
                <jet-input-error :message="form.errors.address1" class="mt-2" />
            </div>

            <!-- Address 2 -->
            <div class="col-span-6 sm:col-span-6">
                <jet-label for="address2" value="Address2" />
                <input
                    id="address2"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.address2"
                />
                <jet-input-error :message="form.errors.address2" class="mt-2" />
            </div>

            <!-- City -->
            <div class="col-span-2 sm:col-span-2">
                <jet-label for="city" value="City" />
                <input
                    id="city"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.city"
                />
                <jet-input-error :message="form.errors.city" class="mt-2" />
            </div>

            <!-- State -->
            <div class="col-span-2 sm:col-span-2">
                <jet-label for="state" value="State" />
                <multiselect
                    id="state"
                    class="mt-1 multiselect"
                    v-model="form.state"
                    :searchable="true"
                    :create-option="true"
                    :options="optionStates"
                    :classes="multiselectClasses"
                />
                <jet-input-error :message="form.errors.state" class="mt-2" />
            </div>

            <!-- Zip -->
            <div class="col-span-2 sm:col-span-2">
                <jet-label for="zip" value="Zip Code" />
                <input
                    id="zip"
                    type="number"
                    class="mt-1 block w-full"
                    v-model="form.zip"
                    maxlength="5"
                />
                <jet-input-error :message="form.errors.zip" class="mt-2" />
            </div>

            <!-- Contact Preference -->
            <div class="col-span-2 sm:col-span-2">
                <jet-label
                    for="contact_preference"
                    value="Contact Preference"
                />
                <select
                    id="contact_preference"
                    class="block w-full mt-1"
                    v-model="form.contact_preference"
                >
                    <option value="email">Email</option>
                    <option value="sms">Text Message</option>
                </select>
                <jet-input-error
                    :message="form.errors.contact_preference"
                    class="mt-2"
                />
            </div>
        </template>

        <template #actions>
            <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </jet-action-message>

            <Button
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing || !form.isDirty"
            >
                Save
            </Button>
        </template>
    </jet-form-section>
</template>

<script setup>
import { ref } from "vue";
import { computed } from "@vue/reactivity";
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";

import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import Multiselect from "@vueform/multiselect";
import { getDefaultMultiselectTWClasses } from "@/utils";
import states from "@/Pages/Comms/States/statesOfUnited";
import { useGymRevForm } from "@/utils";
import { Inertia } from "@inertiajs/inertia";

const props = defineProps({
    user: {
        type: Object,
    },
});

const form = useGymRevForm({
    _method: "PUT",
    id: props.user.id,
    first_name: props.user["first_name"] ?? "",
    last_name: props.user["last_name"],
    address1: props.user["address1"] ?? "",
    address2: props.user["address2"] ?? "",
    city: props.user["city"] ?? "",
    state: props.user["state"] ?? "",
    zip: props.user["zip"] ?? "",
    job_title: props.user["job_title"],
    email: props.user.email,
    alternate_email: props.user.alternate_email ?? "",
    phone: props.user.phone ?? "",
    photo: null,
    contact_preference: props.user.contact_preference?.value,
});
const photo = ref(null);
const photoPreview = ref(null);
const multiselectClasses = ref(getDefaultMultiselectTWClasses());

const optionStates = computed({
    get() {
        let optionsStates = [];
        for (let x in states) {
            optionsStates.push(states[x].abbreviation);
        }

        return optionsStates;
    },
});

function upperCaseF(text) {
    form.state = text.toUpperCase();
}
function updateProfileInformation() {
    if (photo.value) {
        form.photo = photo.value.files[0];
    }

    form.post(route("user-profile-information.update"), {
        errorBag: "updateProfileInformation",
        preserveScroll: true,
        onSuccess: () => clearPhotoFileInput(),
    });
}

function selectNewPhoto() {
    photo.click();
}

function updatePhotoPreview() {
    const photo = photo.files[0];

    if (!photo) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };

    reader.readAsDataURL(photo);
}

function deletePhoto() {
    Inertia.delete(route("current-user-photo.destroy"), {
        preserveScroll: true,
        onSuccess: () => {
            photoPreview.value = null;
            clearPhotoFileInput();
        },
    });
}

function clearPhotoFileInput() {
    if (photo?.value) {
        photo.value = null;
    }
}
</script>
