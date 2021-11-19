<template>
    <span class="badge" :class="badgeColors">{{ text }}</span>
</template>

<script>
//@TODO: consider factoring out some of these components that are not needed now that we have daisy. no need for a component
// for presentational components with daisy alternatives.  when you can just do <div class="badge"/>
export default {
    name: "JetBarBadge",
    props: {
        text: {
            type: String,
            required: true
        },
        type: {
            type: String,
            default: 'info',
            validator: (value) => {
                return ['info', 'success', 'warning', 'danger'].includes(value);
            }
        }
    },
    computed: {
        badgeColors(){
            const colors = {
                "primary":"badge-primary",
                "secondary":"badge-secondary",
                "accent":"badge-accent",
                "info":"badge-info",
                "success": "badge-success",
                "warning":"badge-warning",
                "danger":"badge-error",
                "error":"badge-error"
            }
            return colors[this.type];
        }
    }
}
</script>
