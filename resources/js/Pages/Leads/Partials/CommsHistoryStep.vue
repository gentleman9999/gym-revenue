<template>
    <li class="step step-primary hover:bg-base-100 py-4 cursor-pointer w-32">
        <div class="after">
            <font-awesome-icon :icon="icon"/>
        </div>
        <div>
            <p class="opacity-50">{{ heading }}</p>
            <p class="font-bold">{{ date }}</p>
        </div>
    </li>
</template>
<style scoped>
.step::after {
    content: unset;
}

.after {
    @apply bg-primary text-primary-content
}

.after {
    @apply bg-primary rounded-full grid relative text-base-content;
    z-index: 1;
    place-items: center;
    place-self: center;
    height: 2rem;
    width: 2rem;
    grid-column-start: 1;
    grid-row-start: 1;
}
</style>
<script>
import {computed} from 'vue';
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {
    faChevronDoubleDown,
    faComment,
    faEnvelope,
    faPhoneAlt,
    faUserEdit,
    faUserPlus
} from '@fortawesome/pro-solid-svg-icons';
import {library} from "@fortawesome/fontawesome-svg-core";

library.add(faChevronDoubleDown, faUserPlus, faComment, faEnvelope, faUserEdit, faPhoneAlt);

export default {
    components: {
        FontAwesomeIcon
    },
    props: {
        detail: {
            type: Object,
            required: true
        }
    },
    setup(props) {
        const heading = computed(() => {
            switch (props.detail.field) {
                case "called_by_rep":
                    return 'Phone Call';
                case "emailed_by_rep":
                    return 'Email';
                case "sms_by_rep":
                    return 'Text Message';
                case "claimed":
                    return 'Claimed';
                case "created":
                    return 'Created';
                case "updated":
                    return 'Updated';
                case "manual_create":
                    return "Created";
            }
        })
        const icon = computed(() => {
            switch (props.detail.field) {
                case "called_by_rep":
                    return 'phone-alt'
                case "emailed_by_rep":
                    return 'envelope';
                case "sms_by_rep":
                    return 'comment';
                case "claimed":
                    return 'chevron-double-down';
                case "manual_create":
                case "created":
                    return "user-plus";
                case "updated":
                    return 'user-edit';
            }
        })

        const date = computed(() => new Date(props.detail.created_at).toLocaleString());

        return {heading, icon, date}
    }
}
</script>
