<template>
	<div class="jet_filters-list-subnav">
		<div v-for="item in submenuList"
			 :key="item.type"
			 :class="[
			 	'jet_filters-list-subnav-' + item.type,
			 	{ 'jet_filters-list-subnav-active': isItemActive(item.type) }
			 ]"
			 @click="onItemClick(item.type)">
			{{ item.label }} <span>({{ item.count }})</span>
		</div>
	</div>
</template>

<script>
import { defineComponent, computed } from "vue";
import { useGetters } from "@/store/helper.js";
import filters from "@/services/filters.js";

export default defineComponent({
	name: "Submenu",

	setup(props, context) {
		const {
			currentPage,
			submenuList,
		} = useGetters(['currentPage', 'submenuList']);

		// Methods
		const isItemActive = (itemName) => itemName === currentPage.value;

		// Actions
		const onItemClick = (itemName) => {
			filters.clearFiltering();
			filters.goToPage(itemName);
		};

		return {
			isItemActive,
			submenuList,
			onItemClick
		};
	}
});
</script>
