export async function exportToExcel(type = null, filters = null) {
  const api = useApi(); // ✅ move it inside the function

  let query = null;
  if (filters) {
    const filterEntries = Object.entries(filters).map(
      ([key, value]) => `[${key},${value}]`
    );
    query = `filters=[${filterEntries.join(",")}]`;
  }

  // const query = new URLSearchParams(filter).toString();
  const res = await api("/" + type + "?export=1" + (query ? `&${query}` : ""), {
    method: "GET",
  });

  const blob = await res.blob();
  const url = window.URL.createObjectURL(blob);
  const link = document.createElement("a");
  link.href = url;
  link.setAttribute("download", type + ".xlsx");
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}
