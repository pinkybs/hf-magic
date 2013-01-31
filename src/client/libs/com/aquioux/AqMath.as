/*
--------------------------------------------------
AqMath
　拡張 Math
--------------------------------------------------
*/


package com.aquioux{
// パッケージ名をつけるとエラーになる
// Math（トップレベル）をコールしているためらしい

	public class AqMath {

		// -------------------- プロパティ --------------------
		// 黄金比率（内）
		public static const GORLDEN_RATE:Number = ( -1 + Math.sqrt( 5 ) ) / 2;


		// -------------------- メソッド --------------------
		// ラジアン → 度
		public static function r2d( radian:Number ):Number {
			return radian / Math.PI * 180;
		}
		// 度 → ラジアン
		public static function d2r( degree:Number ):Number {
			return degree * Math.PI / 180;
		}


		// 引数で指定した範囲内で、整数の乱数を発生させる
		// 第1引数に指定範囲のうち大きい方、第2引数に小さい方を指定
		// 第2引数は省略可。その場合、指定範囲最小値は 0 になる
		public static function createRandomInt( max:int , min:int=0 ):int {
			// max : 最大値
			// min : 最小値

			return min + Math.floor( Math.random() * ( max - min + 1 ) );
		}

		// 1 か 0 か
		public static function flipCoin():uint {
			return createRandomInt( 1 );
		}
	}
}
