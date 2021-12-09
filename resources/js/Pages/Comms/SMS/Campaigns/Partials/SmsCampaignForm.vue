<template>
    <jet-form-section @submitted="handleSubmit">
        <!--        <template #title>-->
        <!--            Location Details-->
        <!--        </template>-->

        <!--        <template #description>-->
        <!--            {{ buttonText }} a location.-->
        <!--        </template>-->
        <template #form>
            <div class="form-control col-span-6">
                <label for="name" class="label">Name</label>
                <input type="text" v-model="form.name" autofocus  id="name"/>
                <jet-input-error :message="form.errors.name" class="mt-2"/>
            </div>

            <div class="form-control col-span-6 flex flex-row" v-if="canActivate">
                <input type="checkbox" v-model="form.active" autofocus id="active" class="mt-2" :value="true"/>
                <label for="name" class="label ml-4">Activate (allows assigning to Campaigns)</label>
                <jet-input-error :message="form.errors.active" class="mt-2" />
            </div>

            <div class="form-control col-span-3 flex flex-col" v-if="form.active">
                <p> Select an audience.  </p>
                <select v-if="audiences === undefined" v-model="form.audience_id" class="py-2">
                    <option value="">No Audiences Available</option>
                </select>
                <select v-else v-model="form.audience_id" class="py-2">
                    <option value="">Avalaible Audiences</option>
                    <option v-for="(audience, idy) in audiences" :id="idy" :value="audience.id">
                        {{ audience.name }}
                    </option>
                </select>
                <jet-input-error :message="form.errors.audience_id" class="mt-2" />
            </div>

            <div class="form-control col-span-3 flex flex-col" v-if="form.active">
                <p> Select an SMS Template </p>
                <select v-if="templates === undefined" v-model="form.sms_template_id" class="py-2">
                    <option value="">No Templates Available</option>
                </select>
                <select v-else v-model="form.sms_template_id" class="py-2">
                    <option value="">Available Templates</option>
                    <option v-for="(template, idx) in templates" :id="idx" :value="template.id">
                        {{ template.name }}
                    </option>
                </select>
                <jet-input-error :message="form.errors.sms_template_id" class="mt-2" />
            </div>

            <div class="form-control col-span-3 flex flex-col" v-if="form.active">
                <p> Select a firing schedule </p>
                <select v-model="form.schedule" class="py-2">
                    <option value="drip">As Users are Added (Drip)</option>
                    <option value="bulk">All Subscribed Users (Bulk) </option>
                </select>
                <jet-input-error :message="form.errors.schedule" class="mt-2" />
            </div>

            <div class="form-control col-span-3 flex flex-col" v-if="(form.active) && (form.schedule === 'bulk')">
                <p>When should we trigger this email?</p>
                <select v-model="form.schedule_date" class="py-2">
                    <option value="now">Now</option>
                    <option value="1hr">1hr</option>
                </select>
                <jet-input-error :message="form.errors.schedule_date" class="mt-2" />
            </div>


            <!--                <input id="client_id" type="hidden" v-model="form.client_id"/>-->
            <!--                <jet-input-error :message="form.errors.client_id" class="mt-2"/>-->
        </template>

        <template #actions>
            <!--            TODO: navigation links should always be Anchors. We need to extract button css so that we can style links as buttons-->
            <Button type="button" @click="$inertia.visit(route('comms.sms-campaigns'))" :class="{ 'opacity-25': form.processing }" error outline :disabled="form.processing">
                Cancel
            </Button>
            <div class="flex-grow" />
            <Button class="btn-secondary" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"  :loading="form.processing">
                {{ buttonText }}
            </Button>
        </template>
    </jet-form-section>
</template>

<script>
import {useForm} from '@inertiajs/inertia-vue3'
import SmsFormControl from "@/Components/SmsFormControl"
import AppLayout from '@/Layouts/AppLayout'
import Button from '@/Components/Button'
import JetFormSection from '@/Jetstream/FormSection'
import JetInputError from '@/Jetstream/InputError'

export default {
    name: "SmsCampaignForm",
    components: {
        AppLayout,
        Button,
        JetFormSection,
        SmsFormControl,
        JetInputError,
    },
    props: ['clientId', 'campaign', 'canActivate', 'audiences', 'templates'],
    setup(props, context) {
        let campaign = props.campaign;
        let operation = 'Update';
        if (!campaign) {
            campaign = {
                name: null,
                active: false
                // client_id: props.clientId
            }
            operation = 'Create';
        }
        else {
            campaign['schedule_date'] = 'now';
            campaign['schedule'] = 'bulk';
            campaign['sms_template_id'] = '';
            campaign['audience_id'] = '';
        }

        console.log('campaign Params', campaign);
        const form = useForm(campaign)

        let handleSubmit = () => form.put(route('comms.sms-campaigns.update', campaign.id));
        if (operation === 'Create') {
            handleSubmit = () => form.post(route('comms.sms-campaigns.store'));
        }


        return {form, buttonText: operation, handleSubmit}
    },
    data() {
        return {

        }
    }
}
</script>

<style scoped>

</style>
