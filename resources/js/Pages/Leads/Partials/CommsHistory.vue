<template>

    <ul class="w-full steps">
        <!--                        <li v-for="record in records" class="step step-primary">-->
        <!--                            <div>{{ record.text }}</div>-->
        <!--                        </li>-->
        <li class="step step-primary" v-for="detail in details">
            <p v-if="detail.field === 'manual_create'"><b>Lead Was Manually Created inside GymRevenue By {{
                    detail.value
                }} On -</b> {{ detail.created_at }}</p>
            <p v-if="detail.field === 'claimed'"><b>Lead Claimed By {{ detail.misc['user_id'] }} On -</b>
                {{ detail.created_at }}</p>
            <p v-if="detail.field === 'created'"><b>Created</b> {{ new Date(detail.value).toLocaleString() }}</p>
            <p v-if="detail.field === 'updated'"><b>Updated</b> {{ new Date(detail.created_at).toLocaleString() }} by {{ detail.value }}
            </p>
            <p v-if="detail.field === 'emailed_by_rep'"><b>Email</b> {{ new Date(detail.created_at).toLocaleString() }} by
                {{ detail.misc['user_email'] }}</p>
<!--            <p v-if="detail.field === 'emailed_by_rep'"><b>Subject - </b> {{ detail.misc.subject }}</p>-->
<!--            <p class="mb-4" v-if="detail.field === 'emailed_by_rep'"><b>Message - </b> {{ detail.misc.message }}</p>-->
            <p v-if="detail.field === 'called_by_rep'"><b>Call</b> {{ new Date(detail.created_at).toLocaleString() }} by
                {{ detail.misc['user_email'] }}</p>
<!--            <p v-if="detail.field === 'called_by_rep'"><b>Outcome - </b> {{ detail.misc.outcome }}</p>-->
<!--            <p class="mb-4" v-if="detail.field === 'called_by_rep'"><b>Notes - </b> {{ detail.misc.notes }}</p>-->
            <p v-if="detail.field === 'sms_by_rep'"><b>SMS</b> {{ new Date(detail.created_at).toLocaleString() }} by
                {{ detail.misc['user_email'] }}</p>
<!--            <p class="mb-4" v-if="detail.field === 'sms_by_rep'"><b>Message - </b> {{ detail.misc.message }}</p>-->
        </li>
    </ul>
</template>

<script>
export default {
    name: 'CommsHistory',
    props: {
        details: {
            type: Array
        }
    }
}
</script>
