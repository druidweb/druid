import { toast } from './useToast';

export type BannerStyle = 'success' | 'warning' | 'danger';

export interface FlashBanner {
  banner?: string;
  bannerStyle?: BannerStyle;
  [key: string]: unknown;
}

// Shows a server-flashed banner (RedirectResponse::banner()) as a toast. Warnings use the neutral variant.
export function showFlashBanner(flash: FlashBanner | undefined): void {
  if (!flash?.banner) return;

  if (flash.bannerStyle === 'danger') toast.error(flash.banner);
  else if (flash.bannerStyle === 'warning') toast(flash.banner);
  else toast.success(flash.banner);
}
