<template>
<div class="">
    <ul class="w-full steps">
        <!--                        <li v-for="record in records" class="step step-primary">-->
        <!--                            <div>{{ record.text }}</div>-->
        <!--                        </li>-->
        <li class="step step-primary hover:bg-base-100 py-4 cursor-pointer w-32" v-for="detail in details"
            @click="handleClickDetail(detail)">
            <div v-if="detail.field === 'manual_create'"><b>Lead Was Manually Created inside GymRevenue By {{
                    detail.value
                }} On -</b> {{ detail.created_at }}
            </div>
            <div v-if="detail.field === 'claimed'"><b>Lead Claimed By {{ detail.misc['user_id'] }} On -</b>
                {{ detail.created_at }}
            </div>
            <div v-if="detail.field === 'created'"><p class="font-bold">Created</p>
                {{ new Date(detail.value).toLocaleString() }}
            </div>
            <div v-if="detail.field === 'updated'"><p class="font-bold">Updated</p>
                {{ new Date(detail.created_at).toLocaleString() }} by
                {{ detail.value }}
            </div>
            <div v-if="detail.field === 'emailed_by_rep'"><p class="font-bold">Email</p>
                {{ new Date(detail.created_at).toLocaleString() }}
                {{ detail.misc['user_email'] }}
            </div>
            <div v-if="detail.field === 'called_by_rep'"><p class="font-bold">Call</p>
                {{ new Date(detail.created_at).toLocaleString() }}
                {{ detail.misc['user_email'] }}
            </div>
            <div v-if="detail.field === 'sms_by_rep'"><p class="font-bold">SMS</p>
                {{ new Date(detail.created_at).toLocaleString() }}
                {{ detail.misc['user_email'] }}
            </div>
        </li>
    </ul>
    <div v-if="selectedDetail">
        <h1 v-if="selectedDetail.field === 'emailed_by_rep'">Email</h1>
        <h1 v-if="selectedDetail.field === 'called_by_rep'">Call</h1>
        <h1 v-if="selectedDetail.field === 'sms_by_rep'">SMS</h1>

        <div class="form-control" v-if="selectedDetail.field === 'emailed_by_rep'">
            <label class="label">
                <span class="label-text">Subject</span>
            </label>
            <div type="text" readonly class="input input-ghost h-full" style="height:100%;">
                {{ selectedDetail.misc.subject }}
            </div>
        </div>

        <div class="form-control" v-if="selectedDetail.field === 'emailed_by_rep'">
            <label class="label">
                <span class="label-text">Message</span>
            </label>
            <div type="text" readonly class="textarea textarea-ghost h-full" style="height:100%;">
                {{ selectedDetail.misc.message }}
            </div>
        </div>


        <div class="form-control" v-if="selectedDetail.field === 'called_by_rep'">
            <label class="label">
                <span class="label-text">Outcome</span>
            </label>
            <div type="text" readonly class="input input-ghost h-full" style="height:100%;">
                {{ selectedDetail.misc.outcome }}
            </div>
        </div>

        <div class="form-control" v-if="selectedDetail.field === 'called_by_rep'">
            <label class="label">
                <span class="label-text">Notes</span>
            </label>
            <div type="text" readonly class="textarea textarea-ghost h-full" style="height:100%;">
                {{ selectedDetail.misc.notes }}
            </div>
        </div>

        <div class="form-control" v-if="selectedDetail.field === 'sms_by_rep'">
            <label class="label">
                <span class="label-text">Message</span>
            </label>
            <div type="text" readonly class="textarea textarea-ghost h-full" style="height:100%;">
                {{ selectedDetail.misc.message }}
            </div>
        </div>

    </div>
</div>
</template>
<style scoped>
h1 {
    @apply bg-primary font-bold text-2xl px-4 py-4 -ml-4 rounded-lg;
}
</style>
<script>
import {ref} from "vue";

export default {
    name: 'CommsHistory',
    props: {
        details: {
            type: Array
        }
    },
    setup() {
        const selectedDetail = ref(null);
        const handleClickDetail = (detail) => {
            selectedDetail.value = detail;
        }
        return {selectedDetail, handleClickDetail}
    }
}
</script>
