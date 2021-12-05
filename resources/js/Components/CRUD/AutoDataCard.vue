
<template>
    <data-card>
        <template #title>
            <slot name="title">
                <div :title="__title">
                    {{ __title }}
                </div>
            </slot>
        </template>
        <div v-for="(field, index) in __fields" class="col-span-3 truncate">
            <component
                v-if="fields?.component"
                :is="fields[index].component"
            >
                {{ data[field] }}
            </component>
            <template v-else>
                <div class="text-xs text-gray-500">
                    {{ isObject(field) ? field.name : field }}
                </div>

                <vue-json-pretty
                    v-if="isObject(data[field])"
                    :data="data[field]"
                />
                <span
                    v-else
                    :title="
                            isObject(field)
                                ? field.transform(data[field.name])
                                : data[field]
                        "
                >
                        {{
                        isObject(field)
                            ? field.transform(data[field.name])
                            : data[field]
                    }}
                    </span>
            </template>
        </div>
    </data-card>

    <div class="rounded-lg bg-base-100">
        <div class="rounded-t-xl p-4 border-b border-gray-500 truncate">
            <slot name="title">
                <div :title="__title">
                    {{ __title }}
                </div>
            </slot>
        </div>
        <div class="grid grid-cols-6 p-4">
            <div v-for="(field, index) in __fields" class="col-span-3 truncate">
                <component
                    v-if="fields?.component"
                    :is="fields[index].component"
                >
                    {{ data[field] }}
                </component>
                <template v-else>
                    <div class="text-xs text-gray-500">
                        {{ isObject(field) ? field.name : field }}
                    </div>

                    <vue-json-pretty
                        v-if="isObject(data[field])"
                        :data="data[field]"
                    />
                    <span
                        v-else
                        :title="
                            isObject(field)
                                ? field.transform(data[field.name])
                                : data[field]
                        "
                    >
                        {{
                            isObject(field)
                                ? field.transform(data[field.name])
                                : data[field]
                        }}
                    </span>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
import { library } from "@fortawesome/fontawesome-svg-core";
import { faAlignLeft } from "@fortawesome/pro-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import DataCard from "./DataCard";
import { isObject } from "lodash";
import { transforms, defaults as defaultTransforms } from "./transforms";
import VueJsonPretty from "vue-json-pretty";
import "vue-json-pretty/lib/styles.css";

library.add(faAlignLeft);

export default {
    components: {
        FontAwesomeIcon,
        VueJsonPretty,
        DataCard
    },
    props: {
        data: {
            type: Object,
            required: true,
        },
        fields: {
            type: Array,
            required: true,
        },
        modelName: {
            type: String,
            default: "record",
        },
        modelNamePlural: {
            type: String,
        },
        titleField: {
            type: String,
        },
    },
    setup(props) {
        let __fields;
        let __title;
        if (props.titleField) {
            __title = props.data[props.titleField];
        } else {
            __title = props.data.name || props.data.id;
        }

        if (props.fields) {
            __fields = props.fields.map((header) =>
                isObject(header) ? header.label : header
            );
        } else {
            //they didn't profile a fields object, so pick smart defaults
            __fields = Object.keys(props.data);
        }

        let titleKey = props.titleField;
        if (!titleKey) {
            titleKey = props.data.name ? "name" : "id";
        }

        if (__title && __fields.includes(titleKey)) {
            //if we use a default title field, don't display it twice
            console.log("here", __fields.indexOf(titleKey));
            __fields.splice(__fields.indexOf(titleKey), 1);
        }

        __fields = __fields.map((field) =>
            field in defaultTransforms
                ? { name: field, transform: defaultTransforms[field] }
                : field
        );

        return { __fields, isObject, __title };
    },
};
</script>
