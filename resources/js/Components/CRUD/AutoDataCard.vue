<template>
    <data-card>
        <template #title>
            <slot name="title">
                <div :title="title">
                    {{ title }}
                </div>
            </slot>
        </template>
        <template #actions  v-if="Object.values(actions).length" >
            <slot name="actions">
                <crud-actions :actions="actions" :data="data" :base-url="baseUrl"/>
            </slot>
        </template>
        <div v-for="(field, index) in fieldKeys" class="col-span-3 truncate">
            <div class="text-xs text-gray-500">
                {{ isObject(field) ? field.name : field }}
            </div>
            <component v-if="fields?.find((_field)=>_field?.name===field || _field===field)?.component" :is="fields?.find((_field)=>_field?.name===field || _field===field)?.component" v-bind="{[modelName]: data, data }">
                {{ data[field] }}
            </component>
            <template v-else>


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
</template>

<script>
import { library } from "@fortawesome/fontawesome-svg-core";
import { faEllipsisH } from "@fortawesome/pro-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import DataCard from "./DataCard";
import { isObject } from "lodash";
import { defaults as defaultTransforms } from "./transforms";
import CrudActions from "./CrudActions";
import VueJsonPretty from "vue-json-pretty";
import "vue-json-pretty/lib/styles.css";
import {computed} from "vue";

library.add(faEllipsisH);

export default {
    components: {
        FontAwesomeIcon,
        VueJsonPretty,
        DataCard,
        CrudActions
    },
    props: {
        data: {
            type: Object,
            required: true,
        },
        fields: {
            type: Array,
        },
        update: {
            type: Boolean,
            default: true,
        },
        delete: {
            type: Boolean,
            default: true,
        },
        modelName: {
            type: String,
        },
        modelNamePlural: {
            type: String,
        },
        titleField: {
            type: String,
        },
        actions: {
            type: Object,
            default: {}
        },
        baseUrl:{
            type: String,
        }
    },
    setup(props) {
        let __title;
        if (props.titleField) {
            __title = props.data[props.titleField];
        } else {
            __title = props.data.name || props.data.id;
        }


        const fieldKeys = computed(()=> {
            let __fields = [];
            if (props.fields) {
                __fields =  props.fields.map((field) =>
                    isObject(field) ? field.label : field
                )
            }else{
                __fields =  Object.keys(props.data);
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

            return __fields;
        });


        let titleKey = props.titleField;
        if (!titleKey) {
            titleKey = props.data.name ? "name" : "id";
        }

        let __baseUrl = props.baseUrl || props.modelNamePlural || props.modelName+ 's';

        return { fieldKeys, isObject, title: __title, baseUrl:__baseUrl };
    },
};
</script>
