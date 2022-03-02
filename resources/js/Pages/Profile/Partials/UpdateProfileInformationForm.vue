<template>
    <jet-form-section @submitted="updateProfileInformation">
        <template #title>
            Profile Information
        </template>

        <template #description>
            Update your account's profile information and email address.
        </template>

        <template #form>
            <!-- Profile Photo -->
            <div class="col-span-6 sm:col-span-4" v-if="$page.props.jetstream.managesProfilePhotos">
                <!-- Profile Photo File Input -->
                <input type="file" class="hidden"
                            ref="photo"
                            @change="updatePhotoPreview">

                <jet-label for="photo" value="Photo" />

                <!-- Current Profile Photo -->
                <div class="mt-2" v-show="! photoPreview">
                    <img :src="user.profile_photo_url" :alt="user.name" class="rounded-full h-20 w-20 object-cover">
                </div>

                <!-- New Profile Photo Preview -->
                <div class="mt-2" v-show="photoPreview">
                    <span class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                          :style="'background-image: url(\'' + photoPreview + '\');'">
                    </span>
                </div>

                <jet-secondary-button class="mt-2 mr-2" type="button" @click.prevent="selectNewPhoto">
                    Select A New Photo
                </jet-secondary-button>

                <jet-secondary-button type="button" class="mt-2" @click.prevent="deletePhoto" v-if="user.profile_photo_path">
                    Remove Photo
                </jet-secondary-button>

                <jet-input-error :message="form.errors.photo" class="mt-2" />
            </div>

            <!-- First Name -->
            <div class="col-span-3 sm:col-span-3">
                <jet-label for="first_name" value="First Name" />
                <input id="first_name" type="text" class="mt-1 block w-full" v-model="form['first_name']" autocomplete="name" />
                <jet-input-error :message="form.errors['first_name']" class="mt-2" />
            </div>

            <!-- Last Name -->
            <div class="col-span-3 sm:col-span-3">
                <jet-label for="last_name" value="Last Name" />
                <input id="last_name" type="text" class="mt-1 block w-full" v-model="form['last_name']" autocomplete="name" />
                <jet-input-error :message="form.errors['last_name']" class="mt-2" />
            </div>
            <!-- Name -->
            <div class="col-span-3 sm:col-span-3">
                <jet-label for="name" value="Name" />
                <input id="name" type="text" class="mt-1 block w-full" v-model="form.name" autocomplete="name" />
                <jet-input-error :message="form.errors.name" class="mt-2" />
            </div>

            <!-- Phone # -->
            <div class="col-span-3 sm:col-span-3">
                <jet-label for="phone" value="Phone #" />
                <input id="phone" type="number" class="mt-1 block w-full" v-model="form.phone" />
                <jet-input-error :message="form.errors.phone" class="mt-2" />
            </div>

            <!-- Email -->
            <div class="col-span-3 sm:col-span-3">
                <jet-label for="email" value="Email" />
                <input id="email" type="email" class="mt-1 block w-full" v-model="form.email" />
                <jet-input-error :message="form.errors.email" class="mt-2" />
            </div>

            <!-- AltEmail -->
            <div class="col-span-3 sm:col-span-3">
                <jet-label for="altEmail" value="Personal Email" />
                <input id="altEmail" type="email" class="mt-1 block w-full" v-model="form.altEmail" />
                <jet-input-error :message="form.errors.altEmail" class="mt-2" />
            </div>


            <!-- Address 1 -->
            <div class="col-span-6 sm:col-span-6">
                <jet-label for="address1" value="Address1" />
                <input id="address1" type="text" class="mt-1 block w-full" v-model="form.address1" />
                <jet-input-error :message="form.errors.address1" class="mt-2" />
            </div>

            <!-- Address 2 -->
            <div class="col-span-6 sm:col-span-6">
                <jet-label for="address2" value="Address2" />
                <input id="address2" type="text" class="mt-1 block w-full" v-model="form.address2" />
                <jet-input-error :message="form.errors.address2" class="mt-2" />
            </div>

            <!-- City -->
            <div class="col-span-3 sm:col-span-3">
                <jet-label for="city" value="City" />
                <input id="city" type="text" class="mt-1 block w-full" v-model="form.city" />
                <jet-input-error :message="form.errors.city" class="mt-2" />
            </div>

            <!-- State -->
            <div class="col-span-3 sm:col-span-3">
                <jet-label for="state" value="State" />
                    <multiselect
                        id="state"
                        class="mt-1 multiselect-search"
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
                <input id="zip" type="number" class="mt-1 block w-full" v-model="form.zip" maxlength="5" />
                <jet-input-error :message="form.errors.zip" class="mt-2" />
            </div>

        </template>

        <template #actions>
            <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </jet-action-message>

            <Button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save
            </Button>
        </template>
    </jet-form-section>
</template>

<script>
    import { defineComponent } from 'vue'
    import Button from '@/Components/Button'
    import JetFormSection from '@/Jetstream/FormSection'

    import JetInputError from '@/Jetstream/InputError'
    import JetLabel from '@/Jetstream/Label'
    import JetActionMessage from '@/Jetstream/ActionMessage'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'
    import Multiselect from "@vueform/multiselect";
    import {getDefaultMultiselectTWClasses} from "@/utils";
    import states from "@/Pages/Comms/States/statesOfUnited"

    export default defineComponent({
        components: {
            JetActionMessage,
            Button,
            JetFormSection,

            JetInputError,
            JetLabel,
            JetSecondaryButton,
            'multiselect': Multiselect,
        },

        props: ['user', 'addlData'],

        data() {
            return {
                form: this.$inertia.form({
                    _method: 'PUT',
                    id: this.user.id,
                    'first_name': this.user['first_name'],
                    'last_name': this.user['last_name'],
                    address1: (this.addlData) ? this.addlData.address1 : '',
                    address2: (this.addlData) ? this.addlData.address2 : '',
                    city: (this.addlData) ? this.addlData.city : '',
                    state: (this.addlData) ? this.addlData.state : '',
                    zip: (this.addlData) ? this.addlData.zip : '',
                    jobTitle: (this.addlData) ? this.addlData.jobTitle : '',
                    name: this.user.name,
                    email: this.user.email,
                    phone: (this.addlData) ? this.addlData.phone : '',
                    photo: null,
                }),
                photoPreview: null,
                optionStates: states,
                multiselectClasses: getDefaultMultiselectTWClasses()
            }
        },

        methods: {
            upperCaseF(text) {
                this.form.state = text.toUpperCase();
            },
            updateProfileInformation() {
                if (this.$refs.photo) {
                    this.form.photo = this.$refs.photo.files[0]
                }

                this.form.post(route('user-profile-information.update'), {
                    errorBag: 'updateProfileInformation',
                    preserveScroll: true,
                    onSuccess: () => (this.clearPhotoFileInput()),
                });
            },

            selectNewPhoto() {
                this.$refs.photo.click();
            },

            updatePhotoPreview() {
                const photo = this.$refs.photo.files[0];

                if (! photo) return;

                const reader = new FileReader();

                reader.onload = (e) => {
                    this.photoPreview = e.target.result;
                };

                reader.readAsDataURL(photo);
            },

            deletePhoto() {
                this.$inertia.delete(route('current-user-photo.destroy'), {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.photoPreview = null;
                        this.clearPhotoFileInput();
                    },
                });
            },

            clearPhotoFileInput() {
                if (this.$refs.photo?.value) {
                    this.$refs.photo.value = null;
                }
            },
        },
    })
</script>
