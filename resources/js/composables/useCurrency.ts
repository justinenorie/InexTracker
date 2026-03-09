export function useCurrency() {
  const formatMoney = (value: string | number) => {
    const n = typeof value === 'number' ? value : Number(value)
    if (Number.isNaN(n)) return String(value)

    return new Intl.NumberFormat(undefined, {
      style: 'currency',
      currency: 'PHP',
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    }).format(n)
  }

  return {
    formatMoney,
  }
}
