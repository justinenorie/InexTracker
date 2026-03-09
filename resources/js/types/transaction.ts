import type { Category } from './category'

type TransactionType = 'income' | 'expense'

interface Transaction {
  id: string
  user_id?: string
  category_id?: string | null
  amount: string | number
  description?: string | null
  type: TransactionType | string
  transacted_at: string
  deleted_at?: string | null
  created_at?: string
  updated_at?: string
  category?: Category | null
}

export type { Transaction, TransactionType }
