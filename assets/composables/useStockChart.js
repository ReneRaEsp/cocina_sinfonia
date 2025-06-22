import { ref } from "vue";

const useStockChart = () => {
  const items = ref([]);
  const provData = ref({});
  const prodData = ref({});
  const tiposData = ref({});

  const setProvData = () => {
    const providerFrequency = items.value.reduce((acc, item) => {
      const provider = item.p_id;
      acc[provider] = (acc[provider] || 0) + 1;
      return acc;
    }, {});

    const sortedProviders = Object.entries(providerFrequency)
      .map(([provider, count]) => ({ provider, count }))
      .sort((a, b) => b.count - a.count);

    const topThreeProviders = sortedProviders.slice(0, 5);

    provData.value = {
      labels: topThreeProviders.map((provider) => provider.provider),
      datasets: [
        {
          data: topThreeProviders.map((provider) => provider.count),
          borderColor: ["transparent"],
          backgroundColor: [
            "rgba(28,192,130, 1)",
            "rgba(45, 178, 218, 1)",
            "rgba(72, 84, 218, 1)",
            "rgba(164, 104, 212, 0.76)",
            "rgba(21, 98, 69, 0.76)",
          ],
        },
      ],
    };
  };

  const setProdData = () => {
    const itemsByAmount = [...items.value]
      .sort((a, b) => b.amount - a.amount)
      .slice(0, 5);

    prodData.value = {
      labels: itemsByAmount.map((prod) => prod.name),
      datasets: [
        {
          data: itemsByAmount.map((prod) => prod.amount),
          borderColor: ["transparent"],
          backgroundColor: [
            "rgba(28,192,130, 1)",
            "rgba(45, 178, 218, 1)",
            "rgba(72, 84, 218, 1)",
            "rgba(164, 104, 212, 0.76)",
            "rgba(21, 98, 69, 0.76)",
          ],
        },
      ],
    };
  };

  const setTopItemsType = () => {
    const itemsByType = items.value.reduce((acc, item) => {
      const { tf_id } = item;
      acc[tf_id] = acc[tf_id] || [];
      acc[tf_id].push(item);
      return acc;
    }, {});

    const topItemsByType = Object.entries(itemsByType).reduce(
      (acc, [type, itemss]) => {
        const topItem = itemss.sort((a, b) => b.amount - a.amount)[0];
        acc.push(topItem);
        return acc;
      },
      []
    );

    tiposData.value = {
      labels: topItemsByType.map((prod) => prod.tf_id),
      datasets: [
        {
          data: topItemsByType.map((prod) => prod.amount),
          borderColor: ["transparent"],
          backgroundColor: [
            "rgba(28,192,130, 1)",
            "rgba(45, 178, 218, 1)",
            "rgba(72, 84, 218, 1)",
            "rgba(164, 104, 212, 0.76)",
            "rgba(21, 98, 69, 0.76)",
          ],
        },
      ],
    };
  };

  return {
    items,
    provData,
    prodData,
    tiposData,
    setProvData,
    setProdData,
    setTopItemsType,
  };
};

export default useStockChart;
