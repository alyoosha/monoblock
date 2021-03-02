<template>
	<keep-alive>
		<div class="panel panel_filters">
			<div class="panel__content">
				<div class="panel__wrap" v-if="!isEmpty(filterRelations)">
					<fieldset class="panel__formgroup panel__formgroup_radio">
						<h2 class="panel__subtitle">Ограничения от комплектующих</h2>
						<div class="panel__formtitle"
						     v-for="filter, key in filterRelations.required">{{key}}
							<ul class="list list_unstyled tag-group">
								<li class="list-item"
								    v-for="param in filter">
									<template v-if="param.relation_type == 'one-to-many'">
										<template v-if="param.relation_features">
											<span class="tag-block" v-for="item, key in param.relation_features">
												<span class="tag-block__title"
												     :title="param.feature_type_name"
												      :data-component="param.component_id"
												      :data-value="param.relation_features_slug[key]"
												>
													{{param.show_name ? param.feature_type_name + ' - ' + item : item}}
												</span>
											</span>
										</template>
										<template v-else>
											<span class="tag-block" v-for="item, key in param.relation_values">
												<span class="tag-block__title"
												     :title="param.feature_type_name"
												      :data-component="param.component_id"
												      :data-value="param.relation_features_slug[key]"
												>
													{{param.show_name ? param.feature_type_name + ' - ' + item : item}}
												</span>
											</span>
										</template>
									</template>

									<template v-if="param.relation_type == 'size'">
										<span class="tag-block">
											<span class="tag-block__title"
											      :title="param.feature_type_name"
											      :data-component="param.component_id"
											>
												<template v-if="param.value">
													{{ param.show_name ? param.feature_type_name + ' - ' + param.value : param.value}}
												</template>
												<template v-else>
													{{ param.feature_type_name }}
												</template>
											</span>
										</span>
									</template>

									<template v-if="param.relation_type == 'quantity'">
										<span class="tag-block__title"
										      :title="param.feature_type_name"
										      :data-component="param.component_id"
										>
											{{param.show_name ? param.feature_type_name + ' - ' + param.value :
											param.value}}
										</span>
									</template>

									<template v-if="param.relation_type == 'section'">
										<span class="tag-block__title"
										      :title="param.feature_type_name"
										      :data-component="param.component_id"
										>
											{{param.feature_type_name}}
										</span>
									</template>
								</li>
							</ul>
							<button
									class="panel__formclean"
									type="button"
									aria-label="Очистить"
									@click="changeCheckbox($event, 'remove')"
							>X</button>
						</div>
						<div class="panel__formtitle"
						     v-for="filter, key in filterRelations.norequired">{{key}}
							<ul class="list list_unstyled tag-group">
								<li class="list-item"
								    v-for="param in filter">
									<span v-if="param.relation_type == 'section'">
										<span class="tag-block__title"
										      :title="param.feature_type_name"
										      :data-component="param.component_id"
										>
											{{param.feature_type_name}} - Да
										</span>
									</span>
								</li>
							</ul>
							<button
									class="panel__formclean"
									type="button"
									aria-label="Очистить"
									@click="changeCheckbox($event, 'remove')"
							>X</button>
						</div>
					</fieldset>
				</div>
				<div class="panel__header">
					<div class="panel__title">{{section.name}}
						<span class="panel__subtitle">выбор характеристик</span>
					</div>
					<button class="panel__close"
					        type="button"
					        aria-label="Закрыть панель фильтров"
					        @click="closeFilter"
					>
						Х
					</button>
				</div>
				<div class="panel__body">
					<form class="category-page__form"
					      name="form-set-filter"
					      id="form-set-filter"
					      method="get"
					      action=""
					>
						<template v-for="ft of featureTypes">
							<fieldset
								v-if="ft.filter"
								class="category-page__formgroup">
								<div class="category-page__formheader">
									<div class="category-page__label">{{ft.custom_name ? ft.custom_name : ft.name}}{{ft.unit ? ', ' + ft.unit : ''}}</div>
								</div>

								<template v-if="ft.ui_filter_id != undefined && ft.ui_filter.slug == 'multi-selection'">
									<ul v-if="ft.filter_type == 'list' && ft.hasRelation != undefined"
									    class="list list_unstyled filter-list"
									>
										<li v-for="f of ft.features"
										    class="checkbox">
											<label class="checkbox-label">
												<input class="checkbox-control"
												       v-if="ft.features.length == 1"
												       type="checkbox"
												       :name="ft.slug + '%25L' + ft.id + '%25D' + f.id"
												       @change="changeCheckbox($event), setUrl($event)"
												       :value="f.slug"
												       data-is-single="1"
												       data-is-blocked="1"
												       data-is-relation="1"
												       checked
												       disabled
												>
												<input class="checkbox-control"
												       v-if="ft.features.length != 1"
												       type="checkbox"
												       :name="ft.slug + '%25L' + ft.id + '%25D' + f.id"
												       @change="changeCheckbox($event), setUrl($event)"
												       :value="f.slug"
												       data-is-single="0"
												       data-is-blocked="0"
												       data-is-relation="1"
												       checked
												>
												<span class="checkbox-custom">{{decodeURIComponent(f.name)}}</span>
											</label>
										</li>
									</ul>
									<ul v-else-if="ft.filter_type == 'list'"
									    class="list list_unstyled filter-list">
										<li v-for="f in ft.features" class="checkbox">
											<label class="checkbox-label">
												<input class="checkbox-control"
												       type="checkbox"
												       :name="ft.slug + '%25L' + ft.id + '%25D' + f.id"
												       @change="changeCheckbox($event), setUrl($event)"
												       :value="f.slug"
												>
												<span class="checkbox-custom">{{decodeURIComponent(f.name)}}</span>
											</label>
										</li>
									</ul>
									<ul v-if="ft.filter_type == 'string' && ft.hasRelation != undefined"
										class="list list_unstyled filter-list"
									>
										<li v-for="v of ft.values"
										    class="checkbox"
										>
											<label class="checkbox-label">
												<input class="checkbox-control"
												       type="checkbox"
												       v-if="ft.values.length == 1"
												       :name="ft.slug + '%25S' + v.id + '%25T' + ft.id"
												       :value="v.value"
												       @change="changeCheckbox($event), setUrl($event)"
												       data-is-single="1"
												       data-is-blocked="1"
												       data-is-relation="1"
												       checked
												       disabled
												>
												<input class="checkbox-control"
												       type="checkbox"
												       v-if="ft.values.length != 1"
												       :name="ft.slug + '%25S' + v.id + '%25T' + ft.id"
												       :value="v.value"
												       @change="changeCheckbox($event), setUrl($event)"
												       data-is-single="0"
												       data-is-blocked="0"
												       data-is-relation="1"
												       checked
												>
												<span class="checkbox-custom">{{decodeURIComponent(v.value)}}</span>
											</label>
										</li>
									</ul>
									<ul v-else-if="ft.filter_type == 'string'"
									    class="list list_unstyled filter-list"
									>
										<li v-for="v in ft.values" class="checkbox">
											<label class="checkbox-label">
												<input class="checkbox-control"
												       type="checkbox"
												       :name="ft.slug + '%25S' + v.id + '%25T' + ft.id"
												       @change="changeCheckbox($event), setUrl($event)"
												       :value="v.value"
												>
												<span class="checkbox-custom">{{decodeURIComponent(v.value)}}</span>
											</label>
										</li>
									</ul>
								</template>

								<template v-if="ft.ui_filter_id != undefined && ft.ui_filter.slug == 'from-to'">
									<div v-if="ft.filter_type == 'number'">
										<input
												v-if="ft.min"
												class="category-page__input-num"
												type="number"
												:min="ft.min"
												:max="ft.max"
												:name="'from%25N' + ft.slug + '%25R' + ft.id"
												:placeholder="ft.min"
												@change="changeNum($event), setUrl($event)"
												@input="changeNum($event)"
										/>
										<input
												v-if="ft.max"
												class="category-page__input-num"
												type="number"
												:min="ft.min"
												:max="ft.max"
												:name="'to%25N' + ft.slug + '%25R' + ft.id"
												:placeholder="ft.max"
												@change="changeNum($event), setUrl($event)"
												@input="changeNum($event)"
										/>
									</div>
								</template>

								<template v-if="ft.ui_filter_id != undefined && ft.ui_filter.slug == 'single-selection'">
									<template v-if="ft.change_display_value">
										<ul v-if="ft.filter_type == 'list' && ft.hasRelation != undefined"
										    class="list list_unstyled filter-list"
										>
											<li v-for="f of ft.features"
											    class="checkbox">
												<label class="checkbox-label">
													<input class="checkbox-control"
													       v-if="ft.features.length == 1"
													       type="checkbox"
													       :name="ft.slug + '%25L' + ft.id + '%25D' + f.id"
													       @change="changeCheckbox($event), setUrl($event)"
													       :value="f.slug"
													       data-is-single="1"
													       data-is-blocked="1"
													       data-is-relation="1"
													       checked
													       disabled
													>
													<input class="checkbox-control"
													       v-if="ft.features.length != 1"
													       type="checkbox"
													       :name="ft.slug + '%25L' + ft.id + '%25D' + f.id"
													       @change="changeCheckbox($event), setUrl($event)"
													       :value="f.slug"
													       data-is-single="0"
													       data-is-blocked="0"
													       data-is-relation="1"
													       checked
													>
													<span class="checkbox-custom">{{f.name == 1 ? 'Да' : 'Нет'}}</span>
												</label>
											</li>
										</ul>
										<ul v-else-if="ft.filter_type == 'list'"
										    class="list list_unstyled filter-list">
											<li v-for="f in ft.features" class="checkbox">
												<label class="checkbox-label">
													<input class="checkbox-control"
													       type="checkbox"
													       :name="ft.slug + '%25L' + ft.id + '%25D' + f.id"
													       @change="changeCheckbox($event), setUrl($event)"
													       :value="f.slug"
													>
													<span class="checkbox-custom">{{f.name == 1 ? 'Да' : 'Нет'}}</span>
												</label>
											</li>
										</ul>

										<ul v-if="ft.filter_type == 'number' && ft.hasRelation != undefined"
										    class="list list_unstyled filter-list"
										>
											<li v-for="v of ft.values"
											    class="checkbox"
											>
												<label class="checkbox-label">
													<input class="checkbox-control"
													       type="checkbox"
													       v-if="ft.values.length == 1"
													       :name="ft.slug + '%25S' + v.id + '%25T' + ft.id"
													       :value="v.value"
													       @change="changeCheckbox($event), setUrl($event)"
													       data-is-single="1"
													       data-is-blocked="1"
													       data-is-relation="1"
													       checked
													       disabled
													>
													<input class="checkbox-control"
													       type="checkbox"
													       v-if="ft.values.length != 1"
													       :name="ft.slug + '%25S' + v.id + '%25T' + ft.id"
													       :value="v.value"
													       @change="changeCheckbox($event), setUrl($event)"
													       data-is-single="0"
													       data-is-blocked="0"
													       data-is-relation="1"
													       checked
													>
													<span class="checkbox-custom">{{v.value == 1 ? 'Да' : 'Нет'}}</span>
												</label>
											</li>
										</ul>
										<ul v-else-if="ft.filter_type == 'number'"
										    class="list list_unstyled filter-list"
										>
											<li v-for="v in ft.values" class="checkbox">
												<label class="checkbox-label">
													<input class="checkbox-control"
													       type="checkbox"
													       :name="ft.slug + '%25S' + v.id + '%25T' + ft.id"
													       @change="changeCheckbox($event), setUrl($event)"
													       :value="v.value"
													>
													<span class="checkbox-custom">{{v.value == 1 ? 'Да' : 'Нет'}}</span>
												</label>
											</li>
										</ul>

										<ul v-if="ft.filter_type == 'string' && ft.hasRelation != undefined"
										    class="list list_unstyled filter-list"
										>
											<li v-for="v of ft.values"
											    class="checkbox"
											>
												<label class="checkbox-label">
													<input class="checkbox-control"
													       type="checkbox"
													       v-if="ft.values.length == 1"
													       :name="ft.slug + '%25S' + v.id + '%25T' + ft.id"
													       :value="v.value"
													       @change="changeCheckbox($event), setUrl($event)"
													       data-is-single="1"
													       data-is-blocked="1"
													       data-is-relation="1"
													       checked
													       disabled
													>
													<input class="checkbox-control"
													       type="checkbox"
													       v-if="ft.values.length != 1"
													       :name="ft.slug + '%25S' + v.id + '%25T' + ft.id"
													       :value="v.value"
													       @change="changeCheckbox($event), setUrl($event)"
													       data-is-single="0"
													       data-is-blocked="0"
													       data-is-relation="1"
													       checked
													>
													<span class="checkbox-custom">{{v.value == 1 ? 'Да' : 'Нет'}}</span>
												</label>
											</li>
										</ul>
										<ul v-else-if="ft.filter_type == 'string'"
										    class="list list_unstyled filter-list"
										>
											<li v-for="v in ft.values" class="checkbox">
												<label class="checkbox-label">
													<input class="checkbox-control"
													       type="checkbox"
													       :name="ft.slug + '%25S' + v.id + '%25T' + ft.id"
													       @change="changeCheckbox($event), setUrl($event)"
													       :value="v.value"
													>
													<span class="checkbox-custom">{{v.value == 1 ? 'Да' : 'Нет'}}</span>
												</label>
											</li>
										</ul>

										<ul
											v-if="ft.filter_type == 'boolean'"
											class="list list_unstyled filter-list"
										>
											<li class="checbox">
												<label class="checkbox-label">
													<input class="checkbox-control"
													       type="checkbox"
													       :name="ft.slug + '%25B' + ft.id"
													       value="1"
													       @click="changeCheckbox($event), setConductToCheckbox($event), setUrl($event)"
													>
													<span class="checkbox-custom">Есть</span>
												</label>
											</li>
											<li class="checkbox">
												<label class="checkbox-label">
													<input class="checkbox-control"
													       type="checkbox"
													       :name="ft.slug + '%25B' + ft.id"
													       value="0"
													       @click="changeCheckbox($event), setConductToCheckbox($event), setUrl($event)"
													>
													<span class="checkbox-custom">Нет</span>
												</label>
											</li>
										</ul>
									</template>
									<template v-else>
										<ul v-if="ft.filter_type == 'list' && ft.hasRelation != undefined"
										    class="list list_unstyled filter-list"
										>
											<li v-for="f of ft.features"
											    class="checkbox">
												<label class="checkbox-label">
													<input class="checkbox-control"
													       v-if="ft.features.length == 1"
													       type="checkbox"
													       :name="ft.slug + '%25L' + ft.id + '%25D' + f.id"
													       @change="changeCheckbox($event), setUrl($event)"
													       :value="f.slug"
													       data-is-single="1"
													       data-is-blocked="1"
													       data-is-relation="1"
													       checked
													       disabled
													>
													<input class="checkbox-control"
													       v-if="ft.features.length != 1"
													       type="checkbox"
													       :name="ft.slug + '%25L' + ft.id + '%25D' + f.id"
													       @change="changeCheckbox($event), setUrl($event)"
													       :value="f.slug"
													       data-is-single="0"
													       data-is-blocked="0"
													       data-is-relation="1"
													       checked
													>
													<span class="checkbox-custom">{{decodeURIComponent(f.name)}}</span>
												</label>
											</li>
										</ul>
										<ul v-else-if="ft.filter_type == 'list'"
										    class="list list_unstyled filter-list">
											<li v-for="f in ft.features" class="checkbox">
												<label class="checkbox-label">
													<input class="checkbox-control"
													       type="checkbox"
													       :name="ft.slug + '%25L' + ft.id + '%25D' + f.id"
													       @change="changeCheckbox($event), setUrl($event)"
													       :value="f.slug"
													>
													<span class="checkbox-custom">{{decodeURIComponent(f.name)}}</span>
												</label>
											</li>
										</ul>

										<ul v-if="ft.filter_type == 'number' && ft.hasRelation != undefined"
										    class="list list_unstyled filter-list"
										>
											<li v-for="v of ft.values"
											    class="checkbox"
											>
												<label class="checkbox-label">
													<input class="checkbox-control"
													       type="checkbox"
													       v-if="ft.values.length == 1"
													       :name="ft.slug + '%25S' + v.id + '%25T' + ft.id"
													       :value="v.value"
													       @change="changeCheckbox($event), setUrl($event)"
													       data-is-single="1"
													       data-is-blocked="1"
													       data-is-relation="1"
													       checked
													       disabled
													>
													<input class="checkbox-control"
													       type="checkbox"
													       v-if="ft.values.length != 1"
													       :name="ft.slug + '%25S' + v.id + '%25T' + ft.id"
													       :value="v.value"
													       @change="changeCheckbox($event), setUrl($event)"
													       data-is-single="0"
													       data-is-blocked="0"
													       data-is-relation="1"
													       checked
													>
													<span class="checkbox-custom">{{decodeURIComponent(v.value)}}</span>
												</label>
											</li>
										</ul>
										<ul v-else-if="ft.filter_type == 'number'"
										    class="list list_unstyled filter-list"
										>
											<li v-for="v in ft.values" class="checkbox">
												<label class="checkbox-label">
													<input class="checkbox-control"
													       type="checkbox"
													       :name="ft.slug + '%25S' + v.id + '%25T' + ft.id"
													       @change="changeCheckbox($event), setUrl($event)"
													       :value="v.value"
													>
													<span class="checkbox-custom">{{decodeURIComponent(v.value)}}</span>
												</label>
											</li>
										</ul>

										<ul v-if="ft.filter_type == 'string' && ft.hasRelation != undefined"
										    class="list list_unstyled filter-list"
										>
											<li v-for="v of ft.values"
											    class="checkbox"
											>
												<label class="checkbox-label">
													<input class="checkbox-control"
													       type="checkbox"
													       v-if="ft.values.length == 1"
													       :name="ft.slug + '%25S' + v.id + '%25T' + ft.id"
													       :value="v.value"
													       @change="changeCheckbox($event), setUrl($event)"
													       data-is-single="1"
													       data-is-blocked="1"
													       data-is-relation="1"
													       checked
													       disabled
													>
													<input class="checkbox-control"
													       type="checkbox"
													       v-if="ft.values.length != 1"
													       :name="ft.slug + '%25S' + v.id + '%25T' + ft.id"
													       :value="v.value"
													       @change="changeCheckbox($event), setUrl($event)"
													       data-is-single="0"
													       data-is-blocked="0"
													       data-is-relation="1"
													       checked
													>
													<span class="checkbox-custom">{{decodeURIComponent(v.value)}}</span>
												</label>
											</li>
										</ul>
										<ul v-else-if="ft.filter_type == 'string'"
										    class="list list_unstyled filter-list"
										>
											<li v-for="v in ft.values" class="checkbox">
												<label class="checkbox-label">
													<input class="checkbox-control"
													       type="checkbox"
													       :name="ft.slug + '%25S' + v.id + '%25T' + ft.id"
													       @change="changeCheckbox($event), setUrl($event)"
													       :value="v.value"
													>
													<span class="checkbox-custom">{{decodeURIComponent(v.value)}}</span>
												</label>
											</li>
										</ul>

										<ul
											v-if="ft.filter_type == 'boolean'"
											class="list list_unstyled filter-list"
										>
											<li class="checbox">
												<label class="checkbox-label">
													<input class="checkbox-control"
													       type="checkbox"
													       :name="ft.slug + '%25B' + ft.id"
													       value="1"
													       @click="changeCheckbox($event), setConductToCheckbox($event), setUrl($event)"
													>
													<span class="checkbox-custom">1</span>
												</label>
											</li>
											<li class="checkbox">
												<label class="checkbox-label">
													<input class="checkbox-control"
													       type="checkbox"
													       :name="ft.slug + '%25B' + ft.id"
													       value="0"
													       @click="changeCheckbox($event), setConductToCheckbox($event), setUrl($event)"
													>
													<span class="checkbox-custom">0</span>
												</label>
											</li>
										</ul>
									</template>
								</template>
							</fieldset>
						</template>
					</form>
					<div class="category-page__results">
						<div class="result-item result-item_rec">
							<div class="result-item__content">
								<div class="result-item__label">Рекомендуем</div>
								<div class="result-item__title">Intel® Core™ i5-10600K(F)</div>
								<div class="result-item__price">150 р.</div>
							</div>
							<div class="result-item__control">
								<button class="btn btn_green" type="button">Выбрать</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="panel__footer">
				<div class="row justify-content-center">
					<div class="col-auto">
						<button class="btn btn_blue filter-btn-main"
						    type="button"
						    @click="closeFilter"
						>Найдено - {{countComponents}}
						</button>
					</div>
				</div>
			</div>
		</div>
	</keep-alive>
</template>

<script>
	import {eventBus} from '../../app.js'
    import isEmpty from '../../mixins/is-empty.js'

    export default {
        name: "Index",
        props: {
            raw_feature_types: {
	            default() {
                    return []
	            }
            },
            raw_section: {
                type: Object,
	            default() {
                    return []
	            }
            },
            count_components: {
                type: Number,
                default: 0
            },
            raw_filter_relations: {
                default() {
                    return []
                }
            },
            raw_features: {
                default() {
                    return []
                }
            }
        },
        mixins: [
            isEmpty
        ],
		data: function () {
            return {
                featureTypes: [],
                section: [],
                components: [],
                countComponents: '',
	            dataFilter: {},
	            features: {},
	            setTimeoutId: '',
	            cookie: '',
                filterRelations: []
            }
		},
        beforeMount: function() {
            this.sortFeatureTypes(this.raw_feature_types);
            console.log(this.featureTypes);

	        this.getCookie().forEach((e) => {
	             if(e.includes('pc-constructor')) {
	                 let token = JSON.parse(decodeURIComponent(e).split('=')[1]).token_config;
	                 this.cookie = token;
	             };
	         });

	        this.section = this.raw_section;
	        this.dataFilter['section-slug'] = this.section.slug;
	        this.dataFilter['token'] = this.$store.getters.get_token_config;
	        this.countComponents = this.count_components;
	        this.filterRelations = this.raw_filter_relations;
	        this.features = JSON.parse(this.raw_features);
        },
	    mounted: function () {
            if(!this.isEmpty(this.filterRelations.required)) {
                $('input[type="checkbox"].checkbox-control:not([data-is-relation="1"])').each((e,input) => {
                    $(input).prop('disabled', true);
                });
            }
            if(this.features.length > 0) {
                this.features.forEach(e => {
                    if(e.slug) {
                        if($('input[value="' + e.slug + '"].checkbox-control:not([data-is-single="1"])').length > 0) {
                            $('input[value="' + e.slug + '"].checkbox-control').removeAttr('disabled');
                        }
                    }
                    else {
                        if($('input[value="' + e.value + '"].checkbox-control:not([data-is-single="1"])').length > 0) {
                            $('input[value="' + e.value + '"].checkbox-control').removeAttr('disabled');
                        }
                    }

                });
            }
            else {
                $('input[type="checkbox"].checkbox-control:not([data-is-single="1"])').each((e,input) => {
                    $(input).removeAttr('disabled');
                });
            }

            let vue = this;
            $('#form-set-filter').find ('input, select').each(function() {
	            if((this.type == 'checkbox' || this.type == 'radio') && this.checked && !this.disabled) {
                    vue.dataFilter[this.name] = $(this).val();
	            }
	            if(this.type == 'number' && this.value != "") {
                    vue.dataFilter[this.name] = $(this).val();
	            }
            });
        },
	    watch: {
            countComponents: function () {
                if (this.components.length > 0) {
                    this.countComponents = this.components.length;
                }
            }
	    },
        methods: {
	        changeCheckbox(e, action) {
	            let vue = this;
	            let formtitle;
	            let values = [];

                if(action == 'remove') {
                    this.dataFilter['components'] = [];

                    formtitle = $(e.target).closest('.panel__formtitle');
                    let components = formtitle.find('[data-component]').each(function (k, e) {
                        vue.dataFilter['components'].push(e.dataset.component);
                        values.push(e.dataset.value);
                    });

                    values.forEach(e => {
                        $('input[value="' + e + '"]').prop('disabled', false);
                        $('input[value="' + e + '"]').attr('disabled', false);
                        $('input[value="' + e + '"]').prop('checked', false);
                        $('input[value="' + e + '"]').attr('checked', false);
                        let name = $('input[value="' + e + '"]').attr('name');
                        delete this.dataFilter[name];
                    });
                }

                if(e.target.checked) {
	                $(e.target).attr('checked', 'checked');
	                this.dataFilter[e.target.name] = e.target.value
	            }
	            else {
                    $(e.target).removeAttr('checked', '');
                    delete this.dataFilter[e.target.name];
                }

                this.dataFilter['token_config'] = this.cookie;
                this.dataFilter['action'] = 'delete_component';

                clearTimeout(this.setTimeoutId);
	            this.setTimeoutId = setTimeout(() => {
                    axios
                        .post('/api/catalog/get-components-by-filter', this.dataFilter)
                        .then((response)=> {
                            eventBus.$emit('getComponents', response.data.components);

                            if(action == 'remove') {
                                let allFormtitles = formtitle.closest('.panel__wrap').find('.panel__formtitle');

                                if(allFormtitles.length == 1) {
                                    formtitle.closest('.panel__wrap').remove();
                                }

                                formtitle.remove();
                                this.sortFeatureTypes(response.data.featureTypes);
                            }

                            this.components = response.data.components;

                            vue.countComponents = this.components.length;

                            if(response.data.features.length > 0) {
                                $('input[type="checkbox"].checkbox-control').each((e,input) => {
                                    $(input).prop('disabled', true);
                                });

                                response.data.features.forEach(e => {
                                    if(e.slug) {
                                        if($('input[value="' + e.slug +'"].checkbox-control:not([data-is-single="1"])').length
	                                        > 0) {
                                            $('input[value="' + e.slug + '"].checkbox-control').removeAttr('disabled');
                                        }
                                    }
                                    else {
                                        if($('input[value="' + e.value +'"].checkbox-control:not([data-is-single="1"])').length > 0) {
                                            $('input[value="' + e.value + '"].checkbox-control').removeAttr('disabled');
                                        }
                                    }

                                });
                            }
                            else {
                                $('input[type="checkbox"].checkbox-control:not([data-is-single="1"])').each((e,input) => {
                                    $(input).removeAttr('disabled');
                                });
                            }
                        })
                        .catch(error => {
                            console.log(error);
                            this.errored = true;
                        });
	            }, 700);
	        },
	        changeNum(e) {
	            let vue = this;

	            if(e.target.value == '') return;

                if(e.target.min != '' || e.target.max != '') {
                    if(Number.parseInt(e.target.value) < Number.parseInt(e.target.min)) {
                        e.target.value = "";
                    }
                    else if(Number.parseInt(e.target.value) > Number.parseInt(e.target.max)) {
                        e.target.value = "";
                    }
                }

                this.dataFilter[e.target.name] = e.target.value;

                clearTimeout(this.setTimeoutId);
                this.setTimeoutId = setTimeout(() => {
                    axios
                        .post('/api/catalog/get-components-by-filter', this.dataFilter)
                        .then(response => {
                            eventBus.$emit('getComponents', response.data.components);

                            vue.countComponents = response.data.components.length;

                            $('input[type="checkbox"].checkbox-control').each((e,input) => {
                                $(input).prop('disabled', true);
                            });

                            response.data.features.forEach(e => {
                                if(e.slug) {
                                    if($('input[value="' + e.slug +'"].checkbox-control:not([data-is-single="1"])').length
                                        > 0) {
                                        $('input[value="' + e.slug + '"].checkbox-control').removeAttr('disabled');
                                    }
                                }
                                else {
                                    if($('input[value="' + e.value +'"].checkbox-control:not([data-is-single="1"])').length > 0) {
                                        $('input[value="' + e.value + '"].checkbox-control').removeAttr('disabled');
                                    }
                                }

                            });
                        })
                        .catch(error => {
                            console.log(error);
                            this.errored = true;
                        });
                }, 700);
	        },
	        setUrl(e) {
                let elements = Array.from(document.forms['form-set-filter'].elements);
                let url = '';
                let params = location.search;

                elements.forEach(e => {
                    if(e.dataset.isBlocked != 1) {
                        if((e.type == 'checkbox' || e.type == 'radio') && e.checked) {
                            if(url == '') {
                                url += '?' + e.name + '=' + e.value;
                            }
                            else {
                                url += '&' + e.name + '=' + e.value;
                            }
                        }
                        else if((e.type == 'number' || e.type == 'text') && e.value != '') {
                            if(url == '') {
                                url += '?' + e.name + '=' + e.value;
                            }
                            else {
                                url += '&' + e.name + '=' + e.value;
                            }
                        }
                    }
                });

                url = location.origin + location.pathname + url;

                history.pushState(null, null, url);
	        },
	        setConductToCheckbox(event) {
                if(event.target.checked) {
                    event.target.closest('.filter-list').querySelectorAll('.checkbox-control').forEach(a => {
                        a.removeAttribute('checked');
                        a.checked = false;
                    });

                    event.target.setAttribute('checked', 'checked');
                    event.target.checked = true;
                }
                else {event.target.setAttribute('checked', 'checked');
                    event.target.closest('.filter-list').querySelectorAll('.checkbox-control').forEach(a => {
                        a.removeAttribute('checked');
                        a.checked = false;
                    });
                }
	        },
	        getValue() {
	        },
	        closeFilter(e) {
				$('body').removeClass('panel-filters-open');
	        },
	        getCookie() {
	            return document.cookie.split(';');
	        },
	        sortFeatureTypes(featureTypes) {
	            this.featureTypes = [];

                for(let ft in featureTypes) {
                    if(featureTypes[ft].values != undefined && featureTypes[ft].values.length > 0) {

                        if(featureTypes[ft].filter_type == 'number') {
                            featureTypes[ft].values.sort((a,b) => {
                                return a.value - b.value;
                            });

                            if(featureTypes[ft].values.length > 1) {
                                let first = featureTypes[ft].values.shift();
                                let last = featureTypes[ft].values.pop();
                                featureTypes[ft].values.unshift(first);
                                featureTypes[ft].values.push(last);
                                featureTypes[ft].min = first.value;
                                featureTypes[ft].max = last.value;
                            }
                            else {
                                let first = featureTypes[ft].values.shift();
                                featureTypes[ft].values.unshift(first);
                                featureTypes[ft].min = featureTypes[ft].max = first.value;
                            }
                        }

                    }

                    this.featureTypes.push(featureTypes[ft]);
                }

            }
	    }
    }
</script>

<style scoped>

</style>