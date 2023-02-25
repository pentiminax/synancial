/**
 * @typedef AllocationChart
 * @property {Array<string>} labels
 */

/**
 * @typedef Asset
 * @property {Number} amount
 * @property {Number} share
 */

/**
 * @typedef DashboardData
 * @property {Date} createdAt
 * @property {AllocationChart} allocationChart
 * @property {Distribution} distribution
 * @property {Total} total
 */

/**
 * @typedef Distribution
 * @property {Asset} checking
 * @property {Asset} crowdlendings
 * @property {Asset} loan
 * @property {Asset} market
 * @property {Asset} savings
 */

/**
 * @typedef Total
 * @property {Number} amount
 * @property {Number} netWorth
 * @property {Number} financialAssets
 */


/**
 *
 * @return {Promise<DashboardData>}
 */
export async function getDashboardData() {
    const response = await fetch('/api/users/me/views/dashboard');
    return (await response.json()).result;
}