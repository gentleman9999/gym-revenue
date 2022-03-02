<template>
    <div class="grid grid-cols-6 gap-4">
        <div class="field col-span-6 lg:col-span-3">
            <label>Name:</label>
            <div class="data">
                {{ data.lead.first_name }}
            <span v-if="data.lead.middle_name">{{ data.lead.middle_name.value }}</span>
                {{ data.lead.last_name}}

            </div>
        </div>
        <div class="field col-span-6 lg:col-span-3">
            <label>Email:</label>
            <div class="data">
                {{ data.lead.email}}
            </div>
        </div>
        <div class="field col-span-6 lg:col-span-3">
            <label>Phone:</label>
            <div class="data">
            {{ data.lead.primary_phone }}
            </div>
        </div>
        <div class="field col-span-6 lg:col-span-3">
            <label>Phone secondary:</label>
            <div class="data" v-if="data.lead.alternate_phone">
            {{data.lead.alternate_phone}}
            </div>
        </div>
        <div class="field col-span-6 lg:col-span-3">
            <label>Gender:</label>
            <div class="data" v-if="data.lead.gender">
                {{ data.lead.gender }}
            </div>

        </div>
        <div class="field col-span-6 lg:col-span-3" v-if="data.lead.dob.value">
            <label>Birthdate:</label>
            <div class="data" v-if="data.lead.dob">
             {{ new Date(data.lead.dob.value).toLocaleDateString("en-US") }}
            </div>

        </div>
        <div class="field col-span-6 lg:col-span-3" >
            <label>Lead Owner Email:</label>
            <div class="data" v-if="data.lead.lead_owner" >
              <span class="badge badge-success badge-outline">{{data.lead.lead_owner.misc.user_id}}</span>
            </div>
            <div class="data" v-if="!data.lead.lead_owner">
                <span class="badge badge-error badge-outline">Not Yet Claimed</span>
            </div>
            </div>
        <div class="field col-span-6 lg:col-span-3">
            <label>Contact:</label>

            <div class="data">
                Called: {{data.interactionCount.calledCount}} <br/>
                Emailed: {{data.interactionCount.emailedCount}} <br/>
                Text: {{data.interactionCount.smsCount}} <br/>
            </div>
        </div>

        <div class="field col-span-6 lg:col-span-3">
            <label>Club/Location:</label>
            {{data.lead.gr_location_id}}
            <div class="data">

            </div>
        </div>

<!--
        <div class="field col-span-6 lg:col-span-6">
        {{data}}
        </div>
-->
    </div>
</template>

<style scoped>
input {
    @apply input-xs;
}
.field {
    @apply flex flex-row gap-2;
}
</style>

<script>
export default {
    props: ["data","lead"],
};
</script>
