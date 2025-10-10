/**
 * Pillar Data Validators
 * Centralized validation logic for determining when pillar data should be displayed
 * Used by both CardGenerators and potentially other components
 */

export class PillarDataValidators {
  
  /**
   * Blood Loss - Show if there's amount data or spotting
   */
  static hasBloodLossData(pillar) {
    if (!pillar) return false;
    const amount = pillar.amount ?? 0;
    const spotting = pillar.flags?.spotting;
    return amount > 0 || spotting;
  }

  /**
   * Pain - Show if there's a pain value > 0
   */
  static hasPainData(pillar) {
    if (!pillar) return false;
    const value = pillar.value ?? 0;
    return value > 0;
  }

  /**
   * Impact - Show if there's a grade or limitations
   */
  static hasImpactData(pillar) {
    if (!pillar) return false;
    const grade = pillar.gradeYourDay ?? null;
    const limitations = pillar.limitations || [];
    return grade !== null || limitations.length > 0;
  }

  /**
   * General Health/Energy - Show if there's energy level data
   */
  static hasGeneralHealthData(pillar) {
    if (!pillar) return false;
    const energy = pillar.energyLevel ?? 0;
    return energy > 0;
  }

  /**
   * Mood - Show if there are positive or negative moods
   */
  static hasMoodData(pillar) {
    if (!pillar) return false;
    const positives = pillar.positives || [];
    const negatives = pillar.negatives || [];
    return positives.length > 0 || negatives.length > 0;
  }

  /**
   * Stool/Urine - Show if there's any stool or urine data
   */
  static hasStoolUrineData(pillar) {
    if (!pillar) return false;
    return pillar.stool || pillar.urine;
  }

  /**
   * Sleep - Show if there are calculated hours
   */
  static hasSleepData(pillar) {
    if (!pillar) return false;
    const hours = pillar.calculatedHours ?? 0;
    return hours > 0;
  }

  /**
   * Diet - Show if there are any diet items recorded
   */
  static hasDietData(pillar) {
    if (!pillar) return false;
    const positives = pillar.positives || [];
    const negatives = pillar.negatives || [];
    const neutrals = pillar.neutrals || [];
    return positives.length > 0 || negatives.length > 0 || neutrals.length > 0;
  }

  /**
   * Exercise - Show if exercise was recorded
   */
  static hasExerciseData(pillar) {
    if (!pillar) return false;
    return pillar.any === true;
  }

  /**
   * Sex - Show if there's activity or avoidance data
   */
  static hasSexData(pillar) {
    if (!pillar) return false;
    return pillar.today || pillar.avoided;
  }

  /**
   * Notes - Show if there's a note recorded
   */
  static hasNotesData(pillar) {
    if (!pillar) return false;
    return pillar.hasNote === true;
  }

  /**
   * Generic validator - checks if any pillar has meaningful data
   */
  static hasPillarData(pillar, pillarType) {
    switch (pillarType) {
      case 'blood_loss': return this.hasBloodLossData(pillar);
      case 'pain': return this.hasPainData(pillar);
      case 'impact': return this.hasImpactData(pillar);
      case 'general_health': return this.hasGeneralHealthData(pillar);
      case 'mood': return this.hasMoodData(pillar);
      case 'stool_urine': return this.hasStoolUrineData(pillar);
      case 'sleep': return this.hasSleepData(pillar);
      case 'diet': return this.hasDietData(pillar);
      case 'exercise': return this.hasExerciseData(pillar);
      case 'sex': return this.hasSexData(pillar);
      case 'notes': return this.hasNotesData(pillar);
      default: return false;
    }
  }
}
