<template>
    <jet-form-section @submitted="handleSubmit">
        <template #form>
            <div class="col-span-6 flex flex-col items-start gap-8">
                <div class="w-32 h-32 rounded-full overflow-hidden">
                    <img v-if="fileForm.url" :src="fileForm.url" alt="lead profile picture" class="w-full h-full object-cover"/>
                    <img v-else-if="form?.profile_picture?.misc?.url" :src="form.profile_picture.misc.url" alt="lead profile picture" class="w-full h-full object-cover"/>
                    <font-awesome-icon v-else icon="user-circle" size="6x" class="opacity-10 !h-full !w-full"/>
                </div>
                <label class="btn btn-primary">
                    <span>Upload Image</span>
                    <input
                        @input="fileForm.file =  $event.target.files[0]"
                        type="file"
                        accept="image/*"
                        hidden
                        class="hidden"
                    />
                </label>
            </div>
            <div class="form-control col-span-3" v-if="form['agreement_number']">
                <jet-label for="first_name" value="Agreement Number"/>
                <input disabled type="text" v-model="form['agreement_number']" autofocus class="opacity-70"/>
            </div>
            <div class="form-divider"/>
            <div class="form-control col-span-2">
                <jet-label for="first_name" value="First Name"/>
                <input id="" type="text" v-model="form['first_name']" autofocus/>
                <jet-input-error :message="form.errors['first_name']" class="mt-2"/>
            </div>
            <div class="form-control col-span-2">
                                <jet-label for="middle_name" value="Middle Name"/>
                                <input id="" type="text"
                       v-model="form['middle_name']" autofocus/>
                <jet-input-error :message="form.errors['middle_name']" class="mt-2"/>

            </div>
            <div class="form-control col-span-2">
                <jet-label for="last_name" value="Last Name"/>
                <input id="last_name" type="text" v-model="form[    'last_name']" autofocus/>
                <jet-input-error :message="form.errors['last_name']" class="mt-2"/>
            </div>
            <div class="form-control col-span-6">
                <jet-label for="email" value="Email"/>
                <input id="email" type="email" v-model="form.email" autofocus/>
                <jet-input-error :message="form.errors.email" class="mt-2"/>
            </div>
            <div class="form-control col-span-3">
                <jet-label for="primary_phone" value="Primary Phone"/>
                <input id="primary_phone" type="tel" v-model="form['primary_phone']"
                       autofocus/>
                <jet-input-error :message="form.errors.primary_phone" class="mt-2"/>
            </div>
            <div class="form-control col-span-3">
                <jet-label for="alternate_phone" value="Alternate Phone"/>
                <input id="alternate_phone" type="tel" v-model="form['alternate_phone']"
                       autofocus/>
                <jet-input-error :message="form.errors.alternate_phone" class="mt-2"/>
            </div>
            <div class="form-divider"/>
            <div class="form-control col-span-3">
                <jet-label for="club_id" value="Club"/>
                <select class="" v-model="form['gr_location_id']" required id="club_id">
                    <option value="">Select a Club</option>
                    <option v-for="(name, clubId) in locations" :value="clubId">{{ name }}</option>
                </select>
                <jet-input-error :message="form.errors['gr_location_id']" class="mt-2"/>
            </div>
            <div class="form-control col-span-3">
                <jet-label for="lead_source_id" value="Source"/>
                <select class="" v-model="form['lead_source_id']" required id="lead_source_id">
                    <option value="">Select a Source</option>
                    <option v-for="(source, i) in lead_sources" :value="source.id">{{ source.name }}</option>
                </select>
                <jet-input-error :message="form.errors['lead_source_id']" class="mt-2"/>
            </div>
            <div class="form-control col-span-3">
                <jet-label for="lead_type_id" value="Lead Type"/>
                <select class="" v-model="form['lead_type_id']" required id="lead_type_id">
                    <option value="">Select a Lead Type</option>
                    <option v-for="(lead, i) in lead_types" :value="lead.id">{{ lead.name }}</option>
                </select>
                <jet-input-error :message="form.errors['lead_type_id']" class="mt-2"/>
            </div>
            <div class="form-control col-span-3">
                <jet-label for="membership_type_id" value="Membership Type"/>
                <select class="" v-model="form['membership_type_id']" required id="membership_type_id">
                    <option value="">Select a Membership Type</option>
                    <option v-for="(membership_type, i) in membership_types" :value="membership_type.id">
                        {{ membership_type.name }}
                    </option>
                </select>
                <jet-input-error :message="form.errors['membership_type_id']" class="mt-2"/>
            </div>

        </template>

        <template #actions>
            <!--            TODO: navigation links should always be Anchors. We need to extract button css so that we can style links as buttons-->
            <Button type="button" @click="goBack" :class="{ 'opacity-25': form.processing }" error outline
                    :disabled="form.processing">
                Cancel
            </Button>
            <div class="flex-grow"/>
            <Button :class="{ 'opacity-25': form.processing }" class="btn-primary" :disabled="form.processing"
                    :loading="form.processing">
                {{ buttonText }}
            </Button>
        </template>
    </jet-form-section>
</template>

<script>
import {watchEffect} from "vue";
import {useForm} from '@inertiajs/inertia-vue3'
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {library} from "@fortawesome/fontawesome-svg-core";
import {faUserCircle} from "@fortawesome/pro-solid-svg-icons";
import Vapor from "laravel-vapor";

import AppLayout from '@/Layouts/AppLayout'
import Button from '@/Components/Button'
import JetFormSection from '@/Jetstream/FormSection'
import JetInputError from '@/Jetstream/InputError'
import JetLabel from '@/Jetstream/Label'
import {useGoBack} from '@/utils';

library.add(faUserCircle);

export default {
    components: {
        AppLayout,
        Button,
        JetFormSection,
        FontAwesomeIcon,
        JetInputError,
        JetLabel,
    },
    props: ['clientId', 'lead', 'locations', 'lead_types', 'lead_sources', 'membership_types'],
    setup(props, context) {
        let midname =  usePage().props.value.middle_name.value;
        let lead = props.lead;
        let operation = 'Update';
        if (!lead) {
            lead = {
                first_name: null,
                middle_name: null,
                last_name: null,
                email: null,
                primary_phone: null,//TODO:change to primary/alternate
                alternate_phone: null,
                club_id: null,
                client_id: props.clientId,
                gr_location_id: null,
                lead_type_id: null,
                membership_type_id: null,
                lead_source_id: null,
                profile_picture: null
            }
            operation = 'Create';
        } else {
            lead.agreement_number = lead.details_desc.find(detail => detail.field==='agreement_number').value;
        }

        const form = useForm(lead)
        const fileForm = useForm({file:null});

        let handleSubmit = () => form.put(`/data/leads/${lead.id}`);
        if (operation === 'Create') {
            handleSubmit = () => form.post('/data/leads/create');
        }

        const goBack = useGoBack(route('data.leads'));

        watchEffect(async () => {
            console.log('file Changed!', fileForm.file);
            if(!fileForm.file){
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
                }
            } catch (e) {
                console.error(e);
                // uploadProgress.value = -1;
            }
        })
        return {form, fileForm, buttonText: operation, handleSubmit, goBack,midname }
    },
}
</script>

<style scoped>
input[type=text], input[type=email], input[type=tel] {
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
