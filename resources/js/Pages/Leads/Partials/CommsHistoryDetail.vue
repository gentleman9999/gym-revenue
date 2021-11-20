<template>
    <div>
        <div class="header">
            <h1>{{ heading }}</h1>
            <p>{{detail.created_at}}</p>
        </div>

        <div class="form-control" v-if="detail.field === 'emailed_by_rep'">
            <label class="label">
                <span class="label-text">Subject</span>
            </label>
            <div type="text" readonly class="input input-ghost h-full" style="height:100%;">
                {{ detail.misc.subject }}
            </div>
        </div>

        <div class="form-control" v-if="detail.field === 'emailed_by_rep'">
            <label class="label">
                <span class="label-text">Message</span>
            </label>
            <div type="text" readonly class="textarea textarea-ghost h-full" style="height:100%;">
                {{ detail.misc.message }}
            </div>
        </div>


        <div class="form-control" v-if="detail.field === 'called_by_rep'">
            <label class="label">
                <span class="label-text">Outcome</span>
            </label>
            <div type="text" readonly class="input input-ghost h-full" style="height:100%;">
                {{ detail.misc.outcome }}
            </div>
        </div>

        <div class="form-control" v-if="detail.field === 'called_by_rep'">
            <label class="label">
                <span class="label-text">Notes</span>
            </label>
            <div type="text" readonly class="textarea textarea-ghost h-full" style="height:100%;">
                {{ detail.misc.notes }}
            </div>
        </div>

        <div class="form-control" v-if="detail.field === 'sms_by_rep'">
            <label class="label">
                <span class="label-text">Message</span>
            </label>
            <div type="text" readonly class="textarea textarea-ghost h-full" style="height:100%;">
                {{ detail.misc.message }}
            </div>
        </div>

    </div>
</template>
<style scoped>
.header {
    @apply bg-primary px-4 py-4 -ml-4 rounded-lg;
}
h1 {
    @apply font-bold text-2xl;
}
</style>
<script>

export default {
    props: {
        detail: {
            type: Object
        }
    },
    setup(props) {
        const field = props.detail.field;
        let heading = null;

        switch (field) {
            case "called_by_rep":
                heading = 'Phone Call';
                break;
            case "emailed_by_rep":
                heading = 'Email';
                break;
            case "sms_by_rep":
                heading = 'Text Message';
                break;
            case "claimed":
                heading = 'Claimed';
                break;
            case "created":
                heading = 'Created';
                break;
            case "updated":
                heading = 'Updated';
                break;
        }

        return {heading}
    }
}
</script>
