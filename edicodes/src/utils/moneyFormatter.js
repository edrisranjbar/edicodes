/**
 * Global money formatting utility for the entire application
 * Handles both display formatting and input parsing
 */

// Format price for display (with تومان suffix)
export const formatMoney = (price) => {
  if (!price || price === 0) return '0 تومان';
  const priceStr = price.toString();
  const parts = priceStr.split('.');
  parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  return parts.join('.') + ' تومان';
};

// Format price for display (without تومان suffix) - for input fields
export const formatMoneyInput = (price) => {
  if (!price || price === 0) return '0';
  const priceStr = price.toString();
  const parts = priceStr.split('.');
  parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  return parts.join('.');
};

// Parse formatted price back to number (from input fields)
export const parseMoneyInput = (formattedPrice) => {
  if (!formattedPrice) return 0;
  
  // Remove all non-digit characters except Persian digits
  const cleaned = formattedPrice.replace(/[^\u06F0-\u06F9]/g, '');
  
  // Convert Persian digits to English digits
  const persianToEnglish = {
    '۰': '0', '۱': '1', '۲': '2', '۳': '3', '۴': '4',
    '۵': '5', '۶': '6', '۷': '7', '۸': '8', '۹': '9'
  };
  
  let result = '';
  for (let char of cleaned) {
    result += persianToEnglish[char] || char;
  }
  
  return parseInt(result) || 0;
};

// Format price for labels and display purposes
export const formatPriceLabel = (price) => {
  if (!price || price === 0) return 'رایگان';
  return formatMoney(price);
};

// Format price for tables and lists (compact)
export const formatPriceCompact = (price) => {
  if (!price || price === 0) return '0';
  const priceStr = price.toString();
  const parts = priceStr.split('.');
  parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  return parts.join('.');
};

// Check if a price is free
export const isFree = (price) => {
  return !price || price === 0;
};

// Get currency symbol
export const getCurrencySymbol = () => 'تومان';

// Get currency name
export const getCurrencyName = () => 'تومان';
