<template>
	<keep-alive>
		<div class="category-page">
			<h1 class="category-page__title">{{section.name}}</h1>
			<div>найдено {{components.length}}</div>
			<div class="category-page__results">
				<form class="category-page__form"
				    id="form-choose-component"
				    name="form-choose-component"
				    action="/your-pc"
				    method="post"
				>
					<div class="result-item result-item_rec">
						<div class="result-item__content">
							<div class="result-item__label">Рекомендуем</div>
							<div class="result-item__title">Intel® Core™ i5-10600K(F)</div>
							<div class="result-item__price">150 р.</div>
						</div>
						<div class="result-item__control">
							<button class="btn btn_green" type="submit">Выбрать</button>
						</div>
					</div>
					<div v-for="component in components"
					     :key="component.id"
					     class="result-item">
						<div class="result-item__content">
							<div class="result-item__title">{{component.short_name}}</div>
							<div class="result-item__price">{{component.price}} р.</div>
						</div>
						<div class="result-item__control">
							<button class="btn btn_green"
							    type="button"
							    @click="chooseComponent($event)"
							>Выбрать</button>
							<input class="radio-hidden"
							    type="radio"
							    name="component-id"
							    :value="component.id"
							/>
						</div>
					</div>
					<input type="hidden" name="_token" :value="raw_csrf">
					<input type="hidden"
					       name="action"
					       value="add"
					>
				</form>
			</div>
			<filter-index
				:raw_feature_types="featureTypes"
				:raw_section="section"
				:count_components="components.length"
				:raw_filter_relations="filterRelations"
				:raw_features="features"
			>
			</filter-index>
		</div>
	</keep-alive>
</template>

<script>
	import {eventBus} from '../../app.js'
    import isEmpty from '../../mixins/is-empty'

    export default {
        name: "Index",
        props: {
            raw_components: {
                type: String,
	            default: []
            },
            raw_feature_types: {
                type: String,
	            default: []
            },
            raw_section: {
                type: String,
	            default: []
            },
            raw_filter_relations: {
                type: String,
	            default: []
            },
            raw_features: {
	            default: []
            },
            raw_csrf: {
                type: String,
	            default: ''
            },
        },
	    mixins: [
            isEmpty
	    ],
        data: function () {
            return {
                components: [],
                featureTypes: [],
                section: [],
                filterRelations: [],
                namesComponents: [],
            }
        },
	    created: function() {
          eventBus.$on('getComponents', data => {
				this.components = data;
		    });
	    },
        beforeMount: function() {
			this.components = JSON.parse(this.raw_components);
			this.featureTypes = JSON.parse(this.raw_feature_types);
			this.section = JSON.parse(this.raw_section);
			this.filterRelations = JSON.parse(this.raw_filter_relations);
			this.features = this.raw_features;
        },
	    methods: {
		    chooseComponent(event) {
		        let radio = $(event.target).next();
		        radio.prop('checked', true);
		        let id = radio.val();

		        $('form[name="form-choose-component"]').submit();
		    }
        }
    }
</script>

<style scoped>

</style>